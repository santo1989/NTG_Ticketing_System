<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Create Company
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader"> Company </x-slot>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Company</a></li>
            <li class="breadcrumb-item active">Create Company</li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>


    <x-backend.layouts.elements.errors />
    <form action="{{ route('companies.store') }}" method="post" enctype="multipart/form-data">
        <div>
            @csrf
            <x-backend.form.input name="name" type="text" label="Company Name" />
            <br>
            <x-backend.form.saveButton>Save</x-backend.form.saveButton>



        </div>
    </form>

</x-backend.layouts.master>
