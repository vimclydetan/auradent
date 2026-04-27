<?php
namespace App\Commands;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\EmailService;

class ProcessEmailQueue extends BaseCommand
{
    protected $group       = 'Auradent';
    protected $name        = 'email:process';
    protected $description = 'Process and send queued emails';

    public function run(array $params)
    {
        $limit = $params['limit'] ?? 50;
        $service = new EmailService();
        $results = $service->processQueue((int)$limit);

        CLI::write('✅ Email Queue Processed', 'green');
        CLI::write("Sent: {$results['sent']}");
        CLI::write("Failed: {$results['failed']}", $results['failed'] > 0 ? 'yellow' : 'green');
    }
}