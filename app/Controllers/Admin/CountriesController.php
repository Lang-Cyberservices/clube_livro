<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CountryModel;

class CountriesController extends BaseController
{
    public function index()
    {
        return view('admin/countries/index', [
            'countries' => (new CountryModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function new()
    {
        return view('admin/countries/form', [
            'country' => null,
            'action'  => '/admin/paises',
            'title'   => 'Cadastrar novo país',
        ]);
    }

    public function create()
    {
        $data = $this->getValidatedCountryData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new CountryModel())->insert($data);

        return redirect()->to('/admin/paises')->with('success', 'País cadastrado com sucesso.');
    }

    public function edit(int $id)
    {
        $country = (new CountryModel())->find($id);

        if ($country === null) {
            return redirect()->to('/admin/paises')->with('error', 'País não encontrado.');
        }

        return view('admin/countries/form', [
            'country' => $country,
            'action'  => "/admin/paises/{$id}",
            'title'   => 'Editar país',
        ]);
    }

    public function update(int $id)
    {
        $countryModel = new CountryModel();

        if ($countryModel->find($id) === null) {
            return redirect()->to('/admin/paises')->with('error', 'País não encontrado.');
        }

        $data = $this->getValidatedCountryData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $countryModel->update($id, $data);

        return redirect()->to('/admin/paises')->with('success', 'País atualizado com sucesso.');
    }

    private function getValidatedCountryData(): ?array
    {
        if (! $this->validate([
            'code' => 'required|numeric|max_length[5]',
            'name' => 'required|min_length[2]|max_length[100]',
        ])) {
            return null;
        }

        $phoneMask = trim((string) $this->request->getPost('phone_mask'));

        return [
            'code'       => $this->request->getPost('code'),
            'phone_mask' => $phoneMask !== '' ? $phoneMask : null,
            'name'       => $this->request->getPost('name'),
        ];
    }
}
