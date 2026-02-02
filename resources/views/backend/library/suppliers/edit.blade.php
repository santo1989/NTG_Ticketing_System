<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Edit Supplier Information
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader"> Supplier </x-slot>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
            <li class="breadcrumb-item active">Edit Supplier Information</li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>


    <x-backend.layouts.elements.errors />
    <form action="{{ route('suppliers.update', ['supplier' => $supplier->id]) }}" method="post"
        enctype="multipart/form-data">
        <div class="pb-3">
            @csrf
            @method('put')
            @php
                $companies = App\Models\Company::all();
            @endphp
            <div class="form-group">
                <label for="company_id">Company Name</label>
                <select name="company_id" id="company_id" class="form-control">
                    <option value="">Select Company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}"
                            {{ $supplier->company_id == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <br>
            <br>
            <x-backend.form.input name="name" type="text" label="Supplier Name" :value="$supplier->name" />



            <br>

            <x-backend.form.saveButton>Save</x-backend.form.saveButton>
        </div>
    </form>
</x-backend.layouts.master>
