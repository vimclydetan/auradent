<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuraDent Dental Center | Premium Dental Care Calamba</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">
    <link rel="stylesheet" href="<?= base_url('font-awesome/css/all.min.css') ?>">
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('js/alpine.min.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/tailwind.js') ?>"></script>
    <link rel="icon" href="<?= base_url('assets/images/logo/whitebg_logo.png') ?>" type="image/x-icon">
    <!-- <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" /> -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
        }

        [x-cloak] {
            display: none !important;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .text-gradient {
            background: linear-gradient(to right, #1e40af, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(-5deg);
            background-color: #2563eb;
            color: white;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            border: 2px solid #f8fafc;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
    </style>
</head>

<body data-theme="light"
    x-data="{ 
        loginModal: <?= (session()->getFlashdata('error') && !old('first_name') && !session('errors')) ? 'true' : 'false' ?>, 
        registerModal: <?= (session('errors') || (session()->getFlashdata('error') && old('first_name'))) ? 'true' : 'false' ?>, 
        successAlert: <?= session()->getFlashdata('success') ? 'true' : 'false' ?>,
        mobileMenu: false 
    }"
    class="bg-white text-slate-900">


    <!-- Success Notification Toast -->
    <template x-if="successAlert">
        <div class="toast toast-top toast-end z-[200] mt-20">
            <div class="alert alert-success shadow-2xl border-none bg-emerald-500 text-white rounded-2xl p-4 flex items-start gap-4">
                <div class="bg-white/20 p-2 rounded-xl">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-xs uppercase tracking-widest">Success!</h3>
                    <p class="text-[10px] font-bold opacity-90 uppercase"><?= session()->getFlashdata('success') ?></p>
                </div>
                <button @click="successAlert = false" class="btn btn-ghost btn-xs btn-circle text-white">✕</button>
            </div>
        </div>
    </template>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-[100] glass-nav">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <!-- Logo -->
            <a href="#" class="flex items-center gap-3 group">
                <img src="<?= base_url('assets/images/logo/transparent_logo.png') ?>" alt="Logo" class="w-12 h-12 transition-transform group-hover:scale-105">
                <div class="leading-none">
                    <span class="text-xl font-extrabold tracking-tighter text-slate-800 block uppercase">
                        <?= esc(config('Clinic')->name) ?>
                    </span>
                </div>
            </a>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#services" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Services</a>
                <a href="#location" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Location</a>
                <a href="#contact" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Contact</a>
                <div class="h-4 w-[1px] bg-slate-200"></div>
                <button @click="loginModal = true" class="text-sm font-bold text-slate-700 hover:text-blue-600">Login</button>
                <button @click="registerModal = true" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-full hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                    Book Appointment
                </button>
            </div>

            <!-- Mobile Toggle -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-slate-800">
                <i class="fa-solid fa-bars-staggered text-2xl"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenu" x-transition @click="mobileMenu = false" class="fixed inset-0 bg-slate-900/40 z-[90] md:hidden" x-cloak></div>
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="fixed right-0 top-0 h-full w-3/4 bg-white z-[101] p-8 md:hidden shadow-2xl" x-cloak>
        <div class="flex justify-between items-center mb-12">
            <span class="font-bold text-lg">Menu</span>
            <button @click="mobileMenu = false"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        <div class="space-y-6">
            <a href="#services" @click="mobileMenu = false" class="block text-xl font-bold text-slate-800">Our Services</a>
            <a href="#location" @click="mobileMenu = false" class="block text-xl font-bold text-slate-800">Visit Us</a>
            <a href="#contact" @click="mobileMenu = false" class="block text-xl font-bold text-slate-800">Contact</a>
            <hr class="border-slate-100">
            <button @click="loginModal = true; mobileMenu = false" class="block w-full py-4 bg-slate-50 text-slate-800 rounded-2xl font-bold">Login</button>
            <button @click="registerModal = true; mobileMenu = false" class="block w-full py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-200">Register</button>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden bg-[#fcfdfe]">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div class="z-10 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 border border-blue-100 rounded-full mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    <span class="text-[10px] font-bold text-blue-700 uppercase tracking-widest italic">Now Accepting New Patients</span>
                </div>
                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 leading-[1.1] mb-8">
                    Elevating Every <br><span class="text-gradient">Aura of a Smile.</span>
                </h1>
                <p class="text-lg text-slate-600 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    AuraDent Dental Center combines advanced technology with a gentle touch to provide world-class dental care in the heart of Calamba.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                    <button @click="registerModal = true" class="w-full sm:w-auto px-10 py-5 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition shadow-2xl active:scale-95">
                        Start Your Journey
                    </button>
                    <a href="tel:<?= esc(config('Clinic')->phone) ?>"
                        class="w-full sm:w-auto px-10 py-5 bg-white text-slate-900 border border-slate-200 rounded-2xl font-bold hover:bg-slate-50 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-phone text-blue-600"></i> Call Clinic
                    </a>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-center lg:justify-start gap-8">
                    <div>
                        <p class="text-2xl font-black text-slate-900">5k+</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Happy Smiles</p>
                    </div>
                    <div class="w-[1px] h-10 bg-slate-100"></div>
                    <div>
                        <p class="text-2xl font-black text-slate-900">Expert</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Specialists</p>
                    </div>
                    <div class="w-[1px] h-10 bg-slate-100"></div>
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-[10px] font-bold italic">Top</div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-800 flex items-center justify-center text-white"><i class="fa-solid fa-star text-[10px]"></i></div>
                    </div>
                </div>
            </div>

            <!-- Hero Image Slider -->
            <div class="relative" x-data="{ 
                active: 0, 
                count: 10, 
                init() {
                    setInterval(() => {
                        this.active = (this.active + 1) % this.count;
                    }, 5000);
                }
            }">
                <div class="relative rounded-[2.5rem] sm:rounded-[3.5rem] overflow-hidden shadow-2xl aspect-[4/5] bg-white border border-slate-100 group">
                    <!-- Slides Loop -->
                    <template x-for="i in count" :key="i">
                        <div x-show="active === i-1"
                            x-transition:enter="transition ease-out duration-1000"
                            x-transition:enter-start="opacity-0 scale-105"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-500"
                            class="absolute inset-0 w-full h-full">
                            <img :src="'<?= base_url('assets/images/auradent_') ?>' + i + '.jpg'"
                                class="w-full h-full object-contain"
                                alt="AuraDent Promotion">
                        </div>
                    </template>

                    <!-- Overlay for Dots Readability -->
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-900/20 to-transparent pointer-events-none"></div>

                    <!-- Navigation Dots -->
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2.5 z-20">
                        <template x-for="i in count" :key="i">
                            <button @click="active = i-1"
                                :class="active === i-1 ? 'w-10 bg-blue-600' : 'w-2 bg-slate-300 hover:bg-slate-400'"
                                class="h-2 rounded-full transition-all duration-500"></button>
                        </template>
                    </div>

                    <!-- Manual Navigation (Arrows) -->
                    <button @click="active = active === 0 ? count - 1 : active - 1"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/80 backdrop-blur text-slate-800 opacity-0 group-hover:opacity-100 transition-all shadow-lg hover:bg-blue-600 hover:text-white flex items-center justify-center">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button @click="active = (active + 1) % count"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/80 backdrop-blur text-slate-800 opacity-0 group-hover:opacity-100 transition-all shadow-lg hover:bg-blue-600 hover:text-white flex items-center justify-center">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Decorative Glow Effect sa likod ng image -->
                <div class="absolute -z-10 -top-6 -right-6 w-32 h-32 bg-blue-400/20 rounded-full blur-3xl"></div>
                <div class="absolute -z-10 -bottom-6 -left-6 w-32 h-32 bg-indigo-400/20 rounded-full blur-3xl"></div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-blue-600 font-extrabold tracking-[0.2em] text-xs uppercase mb-4 italic">Complete Solutions</h2>
                    <h3 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight">Advanced Dentistry for Every Member of the Family.</h3>
                </div>
                <div class="hidden md:block">
                    <button class="px-8 py-3 rounded-full border border-slate-200 text-sm font-bold hover:bg-slate-50 transition">View All Procedures</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $services = [
                    ['title' => 'Oral Prophylaxis', 'sub' => 'Deep Cleaning', 'icon' => 'fa-broom'],
                    ['title' => 'Tooth Restoration', 'sub' => 'Fillings / Pasta', 'icon' => 'fa-fill-drip'],
                    ['title' => 'Odontectomy', 'sub' => 'Surgical Removal', 'icon' => 'fa-teeth-open'],
                    ['title' => 'Tooth Extraction', 'sub' => 'Safe Removal', 'icon' => 'fa-scissors'],
                    ['title' => 'Teeth Whitening', 'sub' => 'Laser Brightening', 'icon' => 'fa-wand-magic-sparkles'],
                    ['title' => 'Root Canal', 'sub' => 'Endodontic Care', 'icon' => 'fa-microscope'],
                    ['title' => 'TMD/TMJ Treatment', 'sub' => 'Jaw Alignment', 'icon' => 'fa-head-side-virus'],
                    ['title' => 'Orthodontic Braces', 'sub' => 'Metal & Ceramic', 'icon' => 'fa-grip-lines-vertical'],
                    ['title' => 'Dental Veneers', 'sub' => 'Hollywood Smile', 'icon' => 'fa-face-smile-beam'],
                    ['title' => 'Jacket Crown', 'sub' => 'Protective Caps', 'icon' => 'fa-crown'],
                    ['title' => 'Fixed Bridge', 'sub' => 'Permanent Gap Fix', 'icon' => 'fa-bridge'],
                    ['title' => 'Dentures', 'sub' => 'High-Quality Pustiso', 'icon' => 'fa-teeth'],
                ];

                foreach ($services as $s): ?>
                    <div class="service-card group p-8 rounded-[2.5rem] bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl transition-all duration-500">
                        <div class="service-icon w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm transition-all duration-500 mb-8">
                            <i class="fa-solid <?= $s['icon'] ?> text-xl"></i>
                        </div>
                        <h4 class="text-lg font-extrabold text-slate-900 mb-2"><?= $s['title'] ?></h4>
                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest italic mb-4 opacity-70"><?= $s['sub'] ?></p>
                        <div class="h-1 w-0 group-hover:w-12 bg-blue-600 transition-all duration-500 rounded-full"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- NEW: Location & Clinic Details Section -->
    <section id="location" class="py-24 bg-[#f8fafc] overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-12 gap-16 items-start">

                <!-- Left: Info -->
                <div class="lg:col-span-5">
                    <h2 class="text-blue-600 font-extrabold tracking-[0.2em] text-xs uppercase mb-4 italic">Visit Us Today</h2>
                    <h3 class="text-4xl font-black text-slate-900 mb-8 leading-tight">Your Destination for a Brighter Smile.</h3>

                    <div class="space-y-8">
                        <div class="flex gap-5">
                            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-map-location-dot"></i></div>
                            <div>
                                <h4 class="font-bold text-slate-900">Our Address</h4>
                                <p class="text-slate-600 text-sm leading-relaxed">
                                    <?= esc(config('Clinic')->address) ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-5">
                            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-compass"></i></div>
                            <div>
                                <h4 class="font-bold text-slate-900">Key Landmarks</h4>
                                <p class="text-slate-600 text-sm leading-relaxed">
                                    Located along the Main National Highway. Just across **Shell Halang** and a few steps away from **Liana's Supermarket**. Look for the Dynasty Building.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-5">
                            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-car"></i></div>
                            <div>
                                <h4 class="font-bold text-slate-900">Accessibility</h4>
                                <ul class="text-slate-600 text-sm space-y-2 mt-2">
                                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Dedicated Parking Space for Patients</li>
                                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Wheelchair Accessible Entrance</li>
                                    <li><i class="fa-solid fa-circle-check text-green-500 mr-2"></i> Fully Air-conditioned Lounge & Free Wi-Fi</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <a href="https://maps.app.goo.gl/ypbNr5bf6W7kWFWE7" target="_blank" class="mt-12 inline-flex items-center gap-3 px-8 py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition shadow-xl shadow-blue-200">
                        <i class="fa-solid fa-diamond-turn-right"></i> Get Directions via Google Maps
                    </a>
                </div>

                <!-- Right: Map & Clinic Photos -->
                <div class="lg:col-span-7 grid gap-6">
                    <div class="rounded-[2.5rem] overflow-hidden h-[400px] border-4 border-white shadow-2xl relative group">
                        <iframe class="w-full h-full grayscale group-hover:grayscale-0 transition-all duration-1000" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d929.2285100769737!2d121.16431314996628!3d14.194303478244358!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd6347de33d679%3A0x40d92ddd80c60efd!2sAuraDent%20Dental%20Center!5e1!3m2!1sen!2sus!4v1774950071566!5m2!1sen!2sus"
                            style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="rounded-[2rem] overflow-hidden aspect-video shadow-lg ring-4 ring-white">
                            <img src="<?= base_url('assets/images/exterior/exterior_1.jpg') ?>" class="w-full h-full object-cover" alt="AuraDent Exterior">
                        </div>
                        <div class="rounded-[2rem] overflow-hidden aspect-video shadow-lg ring-4 ring-white">
                            <img src="<?= base_url('assets/images/exterior/exterior_2.jpg') ?>" class="w-full h-full object-cover" alt="AuraDent Interior">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Footer Section -->
    <section id="contact" class="pt-24 pb-12 bg-slate-950 text-white rounded-t-[4rem] lg:rounded-t-[6rem]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-3 gap-16 mb-20">
                <!-- Branding -->
                <div class="space-y-8">
                    <h3 class="text-3xl font-black italic tracking-tighter">AuraDent <span class="text-blue-500">Center</span></h3>
                    <p class="text-slate-400 leading-relaxed">Redefining dental excellence with a focus on patient comfort and modern aesthetics.</p>
                    <div class="flex gap-4">
                        <a href="https://www.facebook.com/auradentdental" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center hover:bg-blue-600 transition-all duration-300"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center hover:bg-blue-600 transition-all duration-300"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-6">Explore</h4>
                        <ul class="space-y-4 text-slate-400 font-medium">
                            <li><a href="#services" class="hover:text-white transition">Services</a></li>
                            <li><a href="#location" class="hover:text-white transition">Location</a></li>
                            <li><a href="#" class="hover:text-white transition">About Us</a></li>
                            <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-6">Contact</h4>
                        <ul class="space-y-4 text-slate-400 text-sm">
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-phone text-blue-500"></i>
                                <?= esc(config('Clinic')->phone) ?>
                            </li>

                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-envelope text-blue-500"></i>
                                <?= esc(config('Clinic')->email) ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Hours -->
                <div class="bg-white/5 p-8 rounded-[3rem] border border-white/10">
                    <h4 class="text-lg font-bold mb-8 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span> Clinic Hours
                    </h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-white/5 pb-4">
                            <span class="text-slate-400">Monday - Saturday</span>
                            <span class="font-bold">9AM - 6PM</span>
                        </div>
                        <div class="flex justify-between items-center pb-4">
                            <span class="text-slate-500 italic uppercase text-[10px] tracking-widest font-bold">Sunday</span>
                            <span class="bg-red-500/10 text-red-400 px-3 py-1 rounded-full text-[10px] font-black uppercase">Closed</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-white/5 text-center">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.3em]">&copy; <?= date('Y') ?> AuraDent Dental Center. Modern Smiles, Professional Care.</p>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div x-show="loginModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4" x-cloak>
        <div x-show="loginModal" x-transition.opacity @click="loginModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="loginModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95 opacity-0 translate-y-4"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0"
            class="relative bg-white w-full max-w-[380px] rounded-[2.5rem] shadow-2xl z-[111] overflow-hidden border border-slate-100">

            <button @click="loginModal = false" class="absolute top-6 right-6 text-slate-300 hover:text-blue-600 transition-colors z-50">
                <i class="fa-solid fa-circle-xmark text-xl"></i>
            </button>

            <div class="p-8 sm:p-10">
                <div class="text-center mb-6">
                    <img src="<?= base_url('assets/images/logo/transparent_logo.png') ?>" class="h-16 mx-auto mb-4">
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic leading-none">Welcome Back</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">AuraDent Dental Center</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="alert alert-error mb-4 py-2.5 px-3 rounded-xl bg-red-50 border-none text-red-600 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                        <span class="text-[10px] font-bold uppercase tracking-tight"><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login') ?>" method="POST" class="space-y-4">
                    <div class="form-control w-full text-left">
                        <label class="label px-1 py-1">
                            <span class="label-text text-[9px] font-bold text-slate-500 uppercase tracking-widest">Username</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="fa-solid fa-user text-xs"></i>
                            </div>
                            <input type="text" name="username" placeholder="Username" required
                                class="input input-bordered w-full h-12 pl-10 bg-slate-50 border-slate-200 rounded-xl focus:outline-blue-500 focus:bg-white transition-all text-sm text-slate-700">
                        </div>
                    </div>

                    <div class="form-control w-full text-left">
                        <label class="label px-1 py-1">
                            <span class="label-text text-[9px] font-bold text-slate-500 uppercase tracking-widest">Password</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                            <input type="password" name="password" placeholder="••••••••" required
                                class="input input-bordered w-full h-12 pl-10 bg-slate-50 border-slate-200 rounded-xl focus:outline-blue-500 focus:bg-white transition-all text-sm text-slate-700">
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-full h-12 bg-blue-600 hover:bg-blue-700 border-none text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-blue-100 mt-2 normal-case text-sm active:scale-95 transition-all">
                        Sign In
                    </button>
                </form>

                <div class="mt-8 pt-4 border-t border-slate-50 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        New Patient?
                        <button @click="loginModal = false; registerModal = true" class="text-blue-600 hover:text-blue-800 underline decoration-2 underline-offset-4 ml-1">
                            Register Here
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div x-show="registerModal"
        class="fixed inset-0 z-[110] flex items-center justify-center p-4"
        x-cloak
        x-data="{ 
            password: '', 
            get requirements() {
                return {
                    length: this.password.length >= 8,
                    upper: /[A-Z]/.test(this.password),
                    number: /[0-9]/.test(this.password),
                    special: /[^A-Za-z0-9]/.test(this.password)
                }
            },
            get strength() {
                let s = 0;
                if (this.requirements.length) s++;
                if (this.requirements.upper) s++;
                if (this.requirements.number) s++;
                if (this.requirements.special) s++;
                return s;
            }
         }">
        <!-- Backdrop -->
        <div x-show="registerModal" x-transition.opacity @click="registerModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <!-- Modal Box -->
        <div x-show="registerModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95 opacity-0 translate-y-4"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0"
            class="relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl z-[111] overflow-hidden flex flex-col max-h-[90vh]">

            <!-- Sticky Header -->
            <div class="px-8 pt-6 pb-2 border-b border-slate-100 bg-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic leading-none">Join AuraDent</h3>
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mt-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i> Create your patient account
                    </p>
                </div>
                <button @click="registerModal = false" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Scrollable Body -->
            <div class="px-8 py-2 overflow-y-auto custom-scrollbar bg-white">

                <!-- Registration General Error Alert -->
                <?php if (session()->getFlashdata('error') && old('first_name')): ?>
                    <div role="alert" class="alert alert-error mb-6 py-3 px-4 rounded-2xl bg-red-50 border-none text-red-600 flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                        <div>
                            <h3 class="text-[10px] font-black uppercase tracking-widest leading-none mb-1">Registration Failed</h3>
                            <p class="text-[10px] font-bold opacity-80 uppercase leading-tight"><?= session()->getFlashdata('error') ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                // Kunin lahat ng errors galing controller
                $valErrors = session('errors') ?? [];
                ?>
                <form action="<?= base_url('register') ?>" method="POST" class="space-y-8">
                    <?= csrf_field() ?>

                    <!-- Section 1: Personal Info -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">01</span>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Personal Information</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">First Name <span class="text-red-500">*</span></span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 <?= isset($valErrors['first_name']) ? 'text-red-400' : 'text-slate-300' ?> text-xs"></i>
                                    <input type="text" name="first_name" value="<?= old('first_name') ?>" placeholder="Juan" required
                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s\-ñÑ]/g, '')"
                                        class="input input-bordered w-full h-12 pl-10 bg-slate-50 rounded-xl transition-all text-sm <?= isset($valErrors['first_name']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                </div>
                                <?php if (isset($valErrors['first_name'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['first_name'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Middle Name</span></label>
                                <input type="text" name="middle_name" value="<?= old('middle_name') ?>" placeholder="Optional"
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s\-ñÑ]/g, '')"
                                    class="input input-bordered w-full h-12 px-4 bg-slate-50 border-slate-200 rounded-xl focus:outline-blue-500 transition-all text-sm">
                            </div>

                            <div class="form-control md:col-span-1">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Last Name <span class="text-red-500">*</span></span></label>
                                <input type="text" name="last_name" value="<?= old('last_name') ?>" placeholder="Dela Cruz" required
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s\-ñÑ]/g, '')"
                                    class="input input-bordered w-full h-12 px-4 bg-slate-50 rounded-xl transition-all text-sm <?= isset($valErrors['last_name']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                <?php if (isset($valErrors['last_name'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['last_name'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Suffix</span></label>
                                <select name="suffix" class="select select-bordered w-full h-12 bg-slate-50 border-slate-200 rounded-xl focus:outline-blue-500 text-sm">
                                    <option value="">None</option>
                                    <option value="Jr." <?= old('suffix') == 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                                    <option value="Sr." <?= old('suffix') == 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                                    <option value="III" <?= old('suffix') == 'III' ? 'selected' : '' ?>>III</option>
                                    <option value="IV" <?= old('suffix') == 'IV' ? 'selected' : '' ?>>IV</option>
                                    <option value="V" <?= old('suffix') == 'V' ? 'selected' : '' ?>>V</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Birthdate <span class="text-red-500">*</span></span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-calendar-day absolute left-4 top-1/2 -translate-y-1/2 <?= isset($valErrors['birthdate']) ? 'text-red-400' : 'text-slate-300' ?> text-xs"></i>
                                    <input type="date" name="birthdate" value="<?= old('birthdate') ?>" required
                                        class="input input-bordered w-full h-12 pl-10 bg-slate-50 rounded-xl text-sm <?= isset($valErrors['birthdate']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                </div>
                                <?php if (isset($valErrors['birthdate'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['birthdate'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Gender <span class="text-red-500">*</span></span></label>
                                <select name="gender" required class="select select-bordered w-full h-12 bg-slate-50 rounded-xl text-sm <?= isset($valErrors['gender']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                    <option value="" disabled <?= !old('gender') ? 'selected' : '' ?>>Select</option>
                                    <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                                <?php if (isset($valErrors['gender'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['gender'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Contact & Security -->
                    <div class="pt-8 border-t border-slate-100">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">02</span>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Account Security</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-control md:col-span-2">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Email Address <span class="text-red-500">*</span></span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 <?= isset($valErrors['email']) ? 'text-red-400' : 'text-slate-300' ?> text-xs"></i>
                                    <input type="email" name="email" value="<?= old('email') ?>" placeholder="email@example.com" required
                                        class="input input-bordered w-full h-12 pl-10 bg-slate-50 rounded-xl text-sm <?= isset($valErrors['email']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                </div>
                                <?php if (isset($valErrors['email'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['email'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Username <span class="text-red-500">*</span></span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 <?= isset($valErrors['username']) ? 'text-red-400' : 'text-slate-300' ?> text-xs"></i>
                                    <input type="text" name="username" value="<?= old('username') ?>" placeholder="j_delacruz" required
                                        class="input input-bordered w-full h-12 pl-10 bg-slate-50 rounded-xl text-sm font-bold text-blue-600 <?= isset($valErrors['username']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                </div>
                                <?php if (isset($valErrors['username'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['username'] ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- ALPINE JS FORMATTED MOBILE FIELD -->
                            <div class="form-control"
                                x-data="{ 
                                    mobile: '<?= old('primary_mobile') ?>',
                                    formatMobile() {
                                        let val = this.mobile.replace(/\D/g, ''); // Tanggalin lahat ng letters/symbols
                                        if (val.length > 11) val = val.substring(0, 11); // Max 11 digits
                                        
                                        // Lagyan ng space para maging 09xx xxx xxxx
                                        if (val.length > 4 && val.length <= 7) {
                                            val = val.substring(0,4) + ' ' + val.substring(4);
                                        } else if (val.length > 7) {
                                            val = val.substring(0,4) + ' ' + val.substring(4,7) + ' ' + val.substring(7);
                                        }
                                        this.mobile = val;
                                    },
                                    initMobile() {
                                        if(!this.mobile || this.mobile === '') {
                                            this.mobile = '09';
                                        } else if (!this.mobile.startsWith('09')) {
                                            this.mobile = '09' + this.mobile.replace(/\D/g, '');
                                            this.formatMobile();
                                        }
                                    }
                                }"
                                x-init="initMobile()">
                                <label class="label"><span class="label-text text-[10px] font-bold text-slate-500 uppercase">Mobile No. <span class="text-red-500">*</span></span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 <?= isset($valErrors['primary_mobile']) ? 'text-red-400' : 'text-slate-300' ?> text-xs"></i>
                                    <input type="text" name="primary_mobile"
                                        x-model="mobile"
                                        @input="formatMobile"
                                        @focus="initMobile"
                                        placeholder="0912 345 6789" required
                                        class="input input-bordered w-full h-12 pl-10 bg-slate-50 rounded-xl text-sm <?= isset($valErrors['primary_mobile']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                                </div>
                                <?php if (isset($valErrors['primary_mobile'])): ?>
                                    <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['primary_mobile'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="form-control mt-5">
                            <label class="label flex justify-between">
                                <span class="label-text text-[10px] font-bold text-slate-500 uppercase">Password <span class="text-red-500">*</span></span>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md"
                                    :class="strength <= 1 ? 'bg-red-50 text-red-500' : strength <= 2 ? 'bg-yellow-50 text-yellow-600' : strength <= 3 ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600'"
                                    x-text="strength <= 1 ? 'Too Weak' : strength <= 2 ? 'Fair' : strength <= 3 ? 'Good' : 'Strong'"></span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 <?= isset($valErrors['password']) ? 'text-red-400' : 'text-slate-300' ?> text-xs"></i>
                                <input type="password" name="password" x-model="password" placeholder="••••••••" required
                                    class="input input-bordered w-full h-12 pl-10 bg-slate-50 rounded-xl text-sm <?= isset($valErrors['password']) ? 'border-red-500 focus:outline-red-500' : 'border-slate-200 focus:outline-blue-500' ?>">
                            </div>
                            <?php if (isset($valErrors['password'])): ?>
                                <span class="text-[9px] text-red-500 font-bold mt-1 uppercase block"><?= $valErrors['password'] ?></span>
                            <?php endif; ?>

                            <!-- Password Strength Visual Bars -->
                            <div class="flex gap-1.5 mt-3 px-1">
                                <template x-for="i in 4">
                                    <div class="h-1.5 w-full rounded-full transition-all duration-500"
                                        :class="i <= strength ? (strength <= 1 ? 'bg-red-500' : strength <= 2 ? 'bg-yellow-500' : strength <= 3 ? 'bg-blue-500' : 'bg-green-500') : 'bg-slate-100'">
                                    </div>
                                </template>
                            </div>

                            <!-- Real-time Requirements Info -->
                            <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Password Requirements:</p>
                                <div class="grid grid-cols-2 gap-y-2 gap-x-4">
                                    <div class="flex items-center gap-2 transition-colors duration-300" :class="requirements.length ? 'text-green-600' : 'text-slate-400'">
                                        <i class="fa-solid" :class="requirements.length ? 'fa-circle-check' : 'fa-circle-dot'"></i>
                                        <span class="text-[10px] font-semibold">8+ Characters</span>
                                    </div>
                                    <div class="flex items-center gap-2 transition-colors duration-300" :class="requirements.upper ? 'text-green-600' : 'text-slate-400'">
                                        <i class="fa-solid" :class="requirements.upper ? 'fa-circle-check' : 'fa-circle-dot'"></i>
                                        <span class="text-[10px] font-semibold">Uppercase Letter</span>
                                    </div>
                                    <div class="flex items-center gap-2 transition-colors duration-300" :class="requirements.number ? 'text-green-600' : 'text-slate-400'">
                                        <i class="fa-solid" :class="requirements.number ? 'fa-circle-check' : 'fa-circle-dot'"></i>
                                        <span class="text-[10px] font-semibold">One Number</span>
                                    </div>
                                    <div class="flex items-center gap-2 transition-colors duration-300" :class="requirements.special ? 'text-green-600' : 'text-slate-400'">
                                        <i class="fa-solid" :class="requirements.special ? 'fa-circle-check' : 'fa-circle-dot'"></i>
                                        <span class="text-[10px] font-semibold">Special Char</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 pb-2">
                        <button type="submit"
                            class="group relative w-full h-14 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl overflow-hidden active:scale-[0.98] transition-all">
                            <span class="relative z-10">Create My Account</span>
                            <div class="absolute inset-0 bg-blue-600 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        </button>

                        <p class="text-center mt-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">
                            Already have an account?
                            <button type="button" @click="registerModal = false; loginModal = true" class="text-blue-600 hover:underline underline-offset-4 ml-1">Log in here</button>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>