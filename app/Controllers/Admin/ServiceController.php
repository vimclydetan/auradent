<?php

namespace App\Controllers\Admin;

use App\Models\ServiceModel;
use App\Controllers\BaseController;

class ServiceController extends BaseController
{
    public function index()
    {
        $model = new ServiceModel();
        $data['services'] = $model->orderBy('service_name', 'ASC')->findAll();
        $data['title'] = "Dental Services";
        return view('admin/services/index', $data);
    }

    public function store()
    {
        $model = new ServiceModel();

        // ✅ FIX: Checkbox only sends value when CHECKED, so default to 0
        $hasLevels = $this->request->getPost('has_levels') === '1' ? 1 : 0;

        // ✅ FIX: Always prepare adjustments, even if empty array
        $durationAdjustments = null;

        if ($hasLevels) {
            $adjustments = [
                'Simple'   => (int)($this->request->getPost('duration_simple') ?? 0),
                'Moderate' => (int)($this->request->getPost('duration_moderate') ?? 0),
                'Severe'   => (int)($this->request->getPost('duration_severe') ?? 0),
            ];

            // ✅ FIX: Encode with error checking
            $durationAdjustments = json_encode($adjustments, JSON_UNESCAPED_UNICODE);

            // Log if encoding failed
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON encode failed: ' . json_last_error_msg());
            }
        }

        $data = [
            'service_name' => $this->request->getPost('service_name'),
            'description'  => $this->request->getPost('description'),
            'has_levels'   => $hasLevels,
            'status'       => 'active',
            'estimated_duration_minutes' => (int)($this->request->getPost('estimated_duration_minutes') ?? 30),
            'duration_adjustments' => $durationAdjustments,  // ✅ This is the only field we keep
            // ❌ Removed: 'level_duration_adjustment'
        ];

        // Pricing logic...
        if ($hasLevels) {
            $data['price_simple']   = $this->request->getPost('price_simple');
            $data['price_moderate'] = $this->request->getPost('price_moderate');
            $data['price_severe']   = $this->request->getPost('price_severe');
            $data['price']          = 0;
        } else {
            $data['price']          = $this->request->getPost('price');
            $data['price_simple']   = null;
            $data['price_moderate'] = null;
            $data['price_severe']   = null;
        }

        $model->save($data);
        return redirect()->to('/admin/services')->with('success', 'Service added successfully!');
    }

    public function update($id)
    {
        $model = new ServiceModel();
        $hasLevels = $this->request->getPost('has_levels') == '1' ? 1 : 0;

        // ✅ Prepare duration adjustments JSON
        $durationAdjustments = null;
        if ($hasLevels) {
            $adjustments = [
                'Simple'   => (int)($this->request->getPost('duration_simple') ?? 0),
                'Moderate' => (int)($this->request->getPost('duration_moderate') ?? 0),
                'Severe'   => (int)($this->request->getPost('duration_severe') ?? 0),
            ];
            // ✅ Encode with clean keys (no trailing spaces)
            $durationAdjustments = json_encode($adjustments, JSON_UNESCAPED_UNICODE);
        }

        $data = [
            'service_name' => $this->request->getPost('service_name'),
            'description'  => $this->request->getPost('description'),
            'has_levels'   => $hasLevels,
            'status'       => $hasLevels ? $this->request->getPost('status') : 'active',
            // ✅ Duration fields - ONLY duration_adjustments
            'estimated_duration_minutes' => (int)($this->request->getPost('estimated_duration_minutes') ?? 30),
            'duration_adjustments'       => $durationAdjustments
        ];

        // Pricing
        if ($hasLevels) {
            $data['price_simple']   = $this->request->getPost('price_simple');
            $data['price_moderate'] = $this->request->getPost('price_moderate');
            $data['price_severe']   = $this->request->getPost('price_severe');
            $data['price']          = 0;
        } else {
            $data['price']          = $this->request->getPost('price');
            $data['price_simple']   = null;
            $data['price_moderate'] = null;
            $data['price_severe']   = null;
        }

        $model->update($id, $data);
        return redirect()->to('/admin/services')->with('success', 'Service updated successfully!');
    }

    public function delete($id)
    {
        $model = new ServiceModel();
        $model->delete($id);
        return redirect()->to('/admin/services')->with('success', 'Service deleted!');
    }
}
