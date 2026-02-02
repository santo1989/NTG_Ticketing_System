<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Edit Buyer Information
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader"> Buyer </x-slot>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('buyers.index') }}">Buyer</a></li>
            <li class="breadcrumb-item active">Edit Buyer Information</li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>


    <x-backend.layouts.elements.errors />
    <form action="{{ route('buyers.update', ['buyer' => $buyer->id]) }}" method="post" enctype="multipart/form-data">
        <div class="pb-3">
            @csrf
            @method('put')
            @php
                $companies = $companies ?? App\Models\Company::all();
            @endphp
            <div class="form-group">
                <label for="company_id">Company Name</label>
                <select name="company_id" id="company_id" class="form-control">
                    <option value="">Select Company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ $buyer->company_id == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <br>
            <x-backend.form.input name="name" type="text" label="Name" :value="$buyer->name" />
            <br>

            <x-backend.form.saveButton>Save</x-backend.form.saveButton>
        </div>
    </form>
</x-backend.layouts.master>
