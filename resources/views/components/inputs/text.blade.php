@props(['id', 'type' => 'text', 'name', 'label'=> null, 'placeholder' => '', 'required' => false])

@if ($label)
    <label for="{{$name}}">{{$label}}</label>
@endif
<input type="{{$type}}" name="{{$name}}" id="{{$id}}" placeholder="{{$placeholder}}" {{$required ? 'required' : ''}}/>