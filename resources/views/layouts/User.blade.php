<x-backend.layouts.master>
    <div class="m-5">
        <h3>Welcome,
            @php
                echo auth()->user()->name;
            @endphp !
        </h3>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            </div>
        </div>
    </div>

</x-backend.layouts.master>
