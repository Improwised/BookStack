@if($image !== '')
<div class="content-wrap p-s">
    <div class="flex-container-row justify-center">
        <img src="{{$image}}" alt="" width="{{$width??'150'}}"  style="object-fit:fill;">
    </div>
</div>
@else
<div class="content-wrap m-s p-s bg-{{$entity}}" style="width: {{$width??'150'}}px;height:50px;">
    <div class="flex-container-column v-end logo-text">
        @icon($entity)
    </div>
</div>
@endif