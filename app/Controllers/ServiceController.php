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
            $data['price_simple']   = $this->request->getPost('price_simple');
            $data['price_moderate'] = $this->request->getPost('price_moderate');
            $data['price_severe']   = $this->request->getPost('price_severe');
            $data['price']          = 0; // Or standard simple price
        } else {
            $data['price']          = $this->request->getPost('price');
            $data['price_simple']   = null;
            $data['price_moderate'] = null;
            $data['price_severe']   = null;
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
}
