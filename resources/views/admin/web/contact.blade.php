@extends('layouts.admin')
@section('title', 'Tampilan Web - Contact Us')
@section('content')
<h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight mb-8">Contact Us</h1>
@if(session('success'))<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('admin.web.contact.update') }}" class="bg-white rounded-2xl border border-gray-100 p-8 max-w-3xl space-y-6">@csrf @method('PUT')
@foreach(['email'=>'Email','instagram'=>'Instagram URL','shopee'=>'Shopee URL','tokopedia'=>'Tokopedia URL','bukalapak'=>'Bukalapak URL'] as $key => $label)<div><label class="block font-label-sm text-xs uppercase tracking-widest mb-2">{{ $label }}</label><input name="{{ $key }}" value="{{ old($key, $contact[$key] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm" required></div>@endforeach
<button class="px-6 py-3 bg-black text-white rounded-full font-bold uppercase text-xs tracking-widest">Simpan Contact Us</button></form>
@endsection
