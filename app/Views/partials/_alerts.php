<!-- app/Views/partials/_alerts.php -->

<?php 
// Listahan ng mga flashdata keys na babantayan
$types = [
    'success' => ['color' => 'emerald', 'icon' => 'fa-check-circle'],
    'error'   => ['color' => 'rose', 'icon' => 'fa-exclamation-circle'],
    'warning' => ['color' => 'amber', 'icon' => 'fa-triangle-exclamation'],
    'info'    => ['color' => 'blue', 'icon' => 'fa-info-circle']
];
?>

<?php foreach ($types as $key => $style): ?>
    <?php if (session()->getFlashdata($key)): ?>
        <div class="mb-4 p-4 bg-<?= $style['color'] ?>-50 text-<?= $style['color'] ?>-800 rounded-xl border border-<?= $style['color'] ?>-200 shadow-sm flex items-start gap-3 animate-fade-in relative overflow-hidden group">
            <!-- Progress Bar (Optional decoration) -->
            <div class="absolute bottom-0 left-0 h-1 bg-<?= $style['color'] ?>-500/20 w-full"></div>
            
            <div class="bg-<?= $style['color'] ?>-500 text-white p-1.5 rounded-lg shrink-0 shadow-sm">
                <i class="fas <?= $style['icon'] ?> text-xs"></i>
            </div>
            
            <div class="flex-1">
                <p class="text-sm font-bold capitalize"><?= $key ?></p>
                <p class="text-xs opacity-90"><?= session()->getFlashdata($key) ?></p>
            </div>
            
            <button onclick="this.parentElement.remove()" class="text-<?= $style['color'] ?>-400 hover:text-<?= $style['color'] ?>-600 transition p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>