/**
 * SlotPicker - Reusable Time Slot Selector Component
 * Vanilla JS, framework-agnostic, configurable
 * 
 * Usage:
 * const picker = new SlotPicker({
 *   apiUrl: '/api/check-availability',
 *   container: '#slotsContainer',
 *   onSelect: (slot) => { console.log('Selected:', slot); },
 *   // ... other config
 * });
 */
class SlotPicker {
    constructor(config) {
        // ===== REQUIRED CONFIG =====
        this.apiUrl = config.apiUrl;
        this.container = config.container; // CSS selector or element
        
        // ===== OPTIONAL CONFIG WITH DEFAULTS =====
        this.csrfName = config.csrfName || 'csrf_test_name';
        this.csrfToken = config.csrfToken || '';
        this.slotDuration = config.slotDuration || 30;
        this.clinicStart = config.clinicStart || '09:00';
        this.clinicEnd = config.clinicEnd || '16:00';
        this.locale = config.locale || 'en-PH';
        
        // ===== CALLBACKS =====
        this.onSelect = config.onSelect || (() => {});
        this.onClear = config.onClear || (() => {});
        this.onLoad = config.onLoad || (() => {});
        this.onError = config.onError || (() => {});
        this.onLoading = config.onLoading || (() => {});
        
        // ===== DOM REFERENCES =====
        this.$container = typeof config.container === 'string' 
            ? document.querySelector(config.container) 
            : config.container;
        
        // ===== STATE =====
        this.selectedSlot = null;
        this.availableSlots = [];
        this.isLoading = false;
        this.config = { dentistId: null, date: null, serviceIds: [], serviceLevels: {} };
        
        // ===== BIND METHODS =====
        this.render = this.render.bind(this);
        this.handleSlotClick = this.handleSlotClick.bind(this);
        
        // Initialize
        this.init();
    }
    
    init() {
        if (!this.$container) {
            console.error('[SlotPicker] Container not found:', this.container);
            return;
        }
        this.$container.dataset.slotPicker = 'true';
    }
    
    // ===== PUBLIC API =====
    
    /**
     * Fetch and render available slots
     * @param {Object} params - { dentistId, date, serviceIds, serviceLevels }
     */
    async fetchSlots(params) {
        this.config = { ...this.config, ...params };
        const { dentistId, date, serviceIds, serviceLevels } = this.config;
        
        if (!date || !serviceIds?.length) {
            this.renderEmpty('Select a date and service to view available times');
            return;
        }
        
        this.setLoading(true);
        
        try {
            const queryString = new URLSearchParams({
                dentist_id: dentistId || '',
                date,
                ...Object.fromEntries(
                    Object.entries(serviceLevels || {}).map(([k, v]) => [`service_levels[${k}]`, v])
                ),
                ...serviceIds.map((id, i) => [`service_ids[${i}]`, id])
            }).toString();
            
            const response = await fetch(`${this.apiUrl}?${queryString}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            if (data.success) {
                this.availableSlots = data.slots || [];
                this.onLoad(data);
                this.render();
            } else {
                this.renderEmpty(data.error || 'No available slots');
                this.onError(data);
            }
        } catch (error) {
            console.error('[SlotPicker] Fetch error:', error);
            this.renderEmpty('Failed to load slots. Please try again.');
            this.onError({ error: error.message });
        } finally {
            this.setLoading(false);
        }
    }
    
    /**
     * Clear selection and re-render
     */
    clearSelection() {
        this.selectedSlot = null;
        this.onClear();
        this.render();
    }
    
    /**
     * Get currently selected slot
     * @returns {Object|null}
     */
    getSelectedSlot() {
        return this.selectedSlot;
    }
    
    /**
     * Update CSRF token (for dynamic forms)
     * @param {string} name 
     * @param {string} token 
     */
    setCsrfToken(name, token) {
        this.csrfName = name;
        this.csrfToken = token;
    }
    
    // ===== PRIVATE METHODS =====
    
    setLoading(isLoading) {
        this.isLoading = isLoading;
        this.onLoading(isLoading);
        if (this.$container) {
            this.$container.classList.toggle('opacity-50', isLoading);
            this.$container.classList.toggle('pointer-events-none', isLoading);
        }
    }
    
    renderEmpty(message) {
        if (!this.$container) return;
        this.$container.innerHTML = `
            <div class="col-span-full text-center py-8 text-[10px] text-slate-400">
                <i class="fas fa-info-circle text-2xl mb-2"></i><br>
                ${message || 'No slots available'}
            </div>
        `;
    }
    
    render() {
        if (!this.$container) return;
        
        // Filter: Bawal mag-start ng 4:00 PM pataas
        const cutoff = this.timeToMinutes(this.clinicEnd);
        let slots = this.availableSlots.filter(slot => 
            this.timeToMinutes(slot.start) < cutoff
        );
        
        // Sort by time
        slots.sort((a, b) => this.timeToMinutes(a.start) - this.timeToMinutes(b.start));
        
        if (slots.length === 0) {
            this.renderEmpty('No available slots for this selection');
            return;
        }
        
        // Group by hour
        const byHour = {};
        slots.forEach(slot => {
            const hour = Math.floor(this.timeToMinutes(slot.start) / 60);
            if (!byHour[hour]) byHour[hour] = [];
            byHour[hour].push(slot);
        });
        
        // Render
        let html = '';
        Object.keys(byHour).sort((a, b) => parseInt(a) - parseInt(b)).forEach(hourKey => {
            const hour = parseInt(hourKey);
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
            
            html += `
                <div class="col-span-full text-[8px] font-black text-slate-400 uppercase tracking-wider mt-1.5 mb-0.5 flex items-center gap-1">
                    <span class="flex-1 h-px bg-slate-200"></span>
                    ${displayHour}:00 ${period}
                    <span class="flex-1 h-px bg-slate-200"></span>
                </div>
            `;
            
            byHour[hourKey].forEach(slot => {
                const isSelected = this.selectedSlot?.start === slot.start;
                const isAvailable = slot.available;
                
                let classes = 'slot-btn relative text-[10px] font-bold py-1.5 px-1.5 rounded-md border transition-all flex flex-col items-center gap-0.5 min-h-[40px] cursor-pointer leading-tight ';
                
                if (isSelected) {
                    classes += 'bg-blue-600 border-blue-600 text-white shadow-md';
                } else if (isAvailable) {
                    classes += 'bg-white border-green-200 text-slate-700 hover:border-green-400 hover:bg-green-50';
                } else {
                    classes += 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed opacity-70';
                }
                
                const displayStart = this.formatTime12h(slot.start);
                const displayEnd = this.formatTime12h(slot.end_display || slot.end);
                const title = isSelected ? 'Selected' : (isAvailable ? 'Click to select' : 'Already booked');
                
                html += `
                    <button type="button" 
                        class="${classes}"
                        ${!isAvailable ? 'disabled tabindex="-1"' : ''}
                        data-start="${slot.start}"
                        data-end="${slot.end}"
                        title="${title}">
                        <span class="font-bold">${displayStart}</span>
                        <span class="text-[9px] opacity-80">${displayEnd}</span>
                        ${!isAvailable ? '<i class="fas fa-lock absolute top-1 right-1 text-[8px]"></i>' : ''}
                        ${isSelected ? '<i class="fas fa-check absolute -top-1 -right-1 w-4 h-4 bg-white text-blue-600 rounded-full text-[9px] flex items-center justify-center shadow"></i>' : ''}
                    </button>
                `;
            });
        });
        
        this.$container.innerHTML = html;
        
        // Attach click listeners
        this.$container.querySelectorAll('.slot-btn:not([disabled])').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const start = e.currentTarget.dataset.start;
                const slot = slots.find(s => s.start === start);
                if (slot) this.handleSlotClick(slot);
            });
        });
    }
    
    handleSlotClick(slot) {
        // Toggle: if same slot clicked, clear it
        if (this.selectedSlot?.start === slot.start) {
            this.clearSelection();
            return;
        }
        
        this.selectedSlot = slot;
        this.render(); // Re-render to update highlight
        this.onSelect(slot);
    }
    
    // ===== UTILITY HELPERS =====
    
    timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const clean = timeStr.replace(/\s*(AM|PM|am|pm)/i, '').trim();
        const [hours, minutes] = clean.split(':').map(Number);
        const isPM = timeStr.toUpperCase().includes('PM');
        const isAM = timeStr.toUpperCase().includes('AM');
        
        let h = hours;
        if (isPM && h !== 12) h += 12;
        if (isAM && h === 12) h = 0;
        
        return h * 60 + (minutes || 0);
    }
    
    formatTime12h(timeStr) {
        if (!timeStr) return '';
        const clean = timeStr.replace(/\s*(AM|PM|am|pm)/i, '').trim();
        let [time, period] = clean.split(' ');
        
        if (!period) {
            let [hours, minutes] = clean.split(':').map(Number);
            period = hours >= 12 ? 'PM' : 'AM';
            if (hours > 12) hours -= 12;
            if (hours === 0) hours = 12;
            return `${hours}:${String(minutes).padStart(2, '0')} ${period}`;
        }
        
        let [hours, minutes] = time.split(':').map(Number);
        if (hours > 12) hours -= 12;
        if (hours === 0) hours = 12;
        return `${hours}:${String(minutes).padStart(2, '0')} ${period.toUpperCase()}`;
    }
    
    formatTimeForDB(timeStr) {
        let [time, period] = timeStr.trim().split(' ');
        let parts = time.split(':');
        let hours = parseInt(parts[0]);
        let minutes = parseInt(parts[1]) || 0;
        let seconds = parts[2] ? parseInt(parts[2]) : 0;
        
        if (period === 'PM' && hours !== 12) hours += 12;
        if (period === 'AM' && hours === 12) hours = 0;
        
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SlotPicker;
}
if (typeof window !== 'undefined') {
    window.SlotPicker = SlotPicker;
}