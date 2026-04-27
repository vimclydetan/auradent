<?php

namespace App\Controllers;

use App\Models\ServiceModel;

class ServiceController extends BaseController
{
    public function index()
    {
        $model = new ServiceModel();
        $data['services'] = $model->findAll();
        $data['title'] = "Dental Services";
        return view('admin/services/index', $data);
    }

    public function store()
    {
        $model = new ServiceModel();

        $hasLevels = $this->request->getPost('has_levels') == '1' ? 1 : 0;

        $data = [
            'service_name' => $this->request->getPost('service_name'),
            'description'  => $this->request->getPost('description'),
            'has_levels'   => $hasLevels,
            'status'       => 'active'
        ];

        if ($hasLevels) {
            $adjustments = [
                'Simple'   => (int)($this->request->getPost('duration_simple') ?? 0),
                'Moderate' => (int)($this->request->getPost('duration_moderate') ?? 0),
                'Severe'   => (int)($this->request->getPost('duration_severe') ?? 0),
            ];
            $data['duration_adjustments'] = json_encode($adjustments, JSON_UNESCAPED_UNICODE);
        } else {
            $data['duration_adjustments'] = null;
        }

        $model->save($data);
        return redirect()->to('/admin/services')->with('success', 'Service added successfully!');
    }

    public function delete($id)
    {
        $model = new ServiceModel();
        $model->delete($id);
        return redirect()->to('/admin/services');
    }

    public function update($id)
    {
        $model = new ServiceModel();

        $hasLevels = $this->request->getPost('has_levels') == '1' ? 1 : 0;

        $data = [
            'service_name' => $this->request->getPost('service_name'),
            'description'  => $this->request->getPost('description'),
            'status'       => $this->request->getPost('status'),
            'has_levels'   => $hasLevels,
        ];

        if ($hasLevels) {
            $adjustments = [
                'Simple'   => (int)($this->request->getPost('duration_simple') ?? 0),
                'Moderate' => (int)($this->request->getPost('duration_moderate') ?? 0),
                'Severe'   => (int)($this->request->getPost('duration_severe') ?? 0),
            ];
            $data['duration_adjustments'] = json_encode($adjustments, JSON_UNESCAPED_UNICODE);
        } else {
            $data['duration_adjustments'] = null;
        }

        $model->update($id, $data);

        return redirect()->to('/admin/services')->with('success', 'Service updated successfully!');
    }
}
