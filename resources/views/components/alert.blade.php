@props(['type', 'message', 'timeout' => '5000'])

@if(session()->has($type))
    <div x-data="{ show: true }" 
        x-init="setTimeout(() => show = false, {{$timeout}})" 
        x-show="show"
        class="p-2 mb-4 text-sm text-black text-center fony-bold rounded {{$type == 'success' ? 'bg-green-300' : 'bg-red-300'}}">
        {{$message}}
    </div>
@endif