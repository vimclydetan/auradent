<?php
// app/Controllers/Admin/UserLogsController.php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Database\ConnectionInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserLogsController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $filters = [
            'username' => $this->request->getGet('username'),
            'status'   => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'  => $this->request->getGet('date_to'),
        ];

        $builder = $this->db->table('login_logs ll')
            ->select('ll.*, u.role, u.email')
            ->join('users u', 'u.id = ll.user_id', 'left')
            ->orderBy('ll.created_at', 'DESC');

        // Filters
        if (!empty($filters['username'])) {
            $builder->like('ll.username', $filters['username']);
        }
        if (!empty($filters['status'])) {
            $builder->where('ll.status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('ll.created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $builder->where('ll.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        // Pagination
        $perPage = 20;
        $page = max(1, (int)$this->request->getGet('page'));

        $logs = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        $total = $builder->countAllResults(false);

        // ✅ MASK IP BEFORE SENDING TO VIEW
        foreach ($logs as &$log) {
            $log['ip_address_masked'] = $this->maskIP($log['ip_address'] ?? '');
        }

        return view('admin/user_logs/index', [
            'title' => 'User Login Logs',
            'logs' => $logs,
            'filters' => $filters,
            'pager' => [
                'current' => $page,
                'total'   => ceil($total / $perPage),
                'count'   => $total
            ],
            'status_options' => ['success', 'wrong_password', 'not_found', 'deactivated']
        ]);
    }


    public function export()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $db = \Config\Database::connect();
        $logs = $db->table('login_logs ll')
            ->select('ll.*, u.username')
            ->join('users u', 'u.id = ll.user_id', 'left')
            ->orderBy('ll.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Login Logs');

        // Merge cells for main header
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'USER LOGS');

        // Main header style - Dark blue background, white text
        $sheet->getStyle('A1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '404040'],
            ],
            'font' => [
                'name' => 'Calibri Light',
                'size' => 11,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(18);


        // Column headers
        $headers = ['ID', 'User ID', 'Username', 'IP Address', 'Status', 'Timestamp'];
        $sheet->fromArray($headers, null, 'A2');

        // Column header style - Gray background
        $sheet->getStyle('A2:F2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '595959'],
            ],
            'font' => [
                'name' => 'Calibri Light',
                'size' => 11,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'B4C6E7'],
                ],
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Data rows
        $rowNum = 3;
        foreach ($logs as $log) {
            $timestamp = !empty($log['created_at']) ? date('n/j/y g:i A', strtotime($log['created_at'])) : '';

            $sheet->fromArray([
                $log['id'],
                $log['user_id'] ?? '',
                $log['username'] ?? '',
                $log['ip_address'] ?? '',
                $log['status'] ?? '',
                $timestamp
            ], null, 'A' . $rowNum);

            // Data row style - Light blue background
            $sheet->getStyle('A' . $rowNum . ':F' . $rowNum)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C5D9F1'],
                ],
                'font' => [
                    'name' => 'Calibri Light',
                    'size' => 11,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'B4C6E7'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($rowNum)->setRowHeight(18);

            $rowNum++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER); // or PAPERSIZE_A4

        // Center horizontally and vertically
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);

        // Set margins (optional - adjust as needed)
        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setHeader(0.3);
        $sheet->getPageMargins()->setFooter(0.3);

        // Set fit to page (optional - para ma-fit sa 1 page kung maliit ang data)
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1); // 1 page wide
        $sheet->getPageSetup()->setFitToHeight(0); // unlimited height

        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="login_logs_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function maskIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return preg_replace('/\.\d+$/', '.xxx', $ip);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return substr($ip, 0, 6) . '****';
        }

        return 'N/A';
    }

    /**
     * Verify password before allowing export
     */
    public function verifyExportPassword()
    {
        // Only allow AJAX requests
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON();
        $password = $input->password ?? '';

        // ✅ I-verify ang password (gamit ang CodeIgniter's password_verify)
        $user = $this->db->table('users')
            ->where('id', session()->get('user_id'))
            ->select('password')
            ->get()
            ->getRow();

        if ($user && password_verify($password, $user->password)) {
            // ✅ Password correct
            return $this->response->setJSON(['success' => true]);
        }

        // ❌ Password wrong
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Incorrect password. Please try again.'
        ]);
    }
}
