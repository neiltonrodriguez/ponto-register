@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Employee</h3>

            <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" required
                            value="{{ old('name', $employee->name) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                        <input type="text" name="cpf" id="cpf" required maxlength="14"
                            value="{{ old('cpf', $employee->formatted_cpf) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('cpf') border-red-500 @enderror">
                        @error('cpf')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required
                            value="{{ old('email', $employee->email) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                        <select name="position" id="position" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror">
                            <option value="">-- Select Position --</option>
                            @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ old('position', $employee->position ?? '') == $pos ? 'selected' : '' }}>
                                {{ $pos }}
                            </option>
                            @endforeach
                        </select>
                        @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date</label>
                        <input type="date" name="birth_date" id="birth_date" required
                            value="{{ old('birth_date', $employee->birth_date->format('Y-m-d')) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('birth_date') border-red-500 @enderror">
                        @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                        <input type="text" name="zip_code" id="zip_code" required maxlength="9"
                            value="{{ old('zip_code', $employee->formatted_zip_code) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('zip_code') border-red-500 @enderror">
                        @error('zip_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" required
                            value="{{ old('address', $employee->address) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700">Number</label>
                        <input type="text" name="number" id="number" required
                            value="{{ old('number', $employee->number) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('number') border-red-500 @enderror">
                        @error('number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="complement" class="block text-sm font-medium text-gray-700">Complement</label>
                        <input type="text" name="complement" id="complement"
                            value="{{ old('complement', $employee->complement) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('complement') border-red-500 @enderror">
                        @error('complement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="neighborhood" class="block text-sm font-medium text-gray-700">Neighborhood</label>
                        <input type="text" name="neighborhood" id="neighborhood" required
                            value="{{ old('neighborhood', $employee->neighborhood) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('neighborhood') border-red-500 @enderror">
                        @error('neighborhood')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city" required
                            value="{{ old('city', $employee->city) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror">
                        @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="state" id="state" required maxlength="2"
                            value="{{ old('state', $employee->state) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('state') border-red-500 @enderror">
                        @error('state')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('admin.employees') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // CPF Mask
    document.getElementById('cpf').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    // ZIP Code Mask
    document.getElementById('zip_code').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = value;
    });

    // ZIP Code Search
    document.getElementById('zip_code').addEventListener('blur', function(e) {
        const zipCode = e.target.value.replace(/\D/g, '');

        if (zipCode.length === 8) {
            fetch(`{{ route('admin.search-zip-code') }}?zip_code=${zipCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('ZIP code not found');
                    } else {
                        document.getElementById('address').value = data.address || '';
                        document.getElementById('neighborhood').value = data.neighborhood || '';
                        document.getElementById('city').value = data.city || '';
                        document.getElementById('state').value = data.state || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error searching ZIP code');
                });
        }
    });
</script>
@endpush
@endsection