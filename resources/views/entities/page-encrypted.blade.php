@extends('layouts.simple')

@section('body')

    <div class="container small">


        <div class="card content-wrap auto-height">
            <form method="post" action="{{ $url }}">
            {!! csrf_field() !!}
            <h1 class="list-heading">Export as Encrypted or Decrypted</h1>

            
            <p class="text-warn">In Your {{$entity->getType()}} some page or pages is encrypted want to decrypt or export as encrypted</p>

            <div class="grid half v-center">
                <!-- <div>
                    <p class="text-neg">
                        <strong>
                            {{ $page->draft ? trans('entities.pages_delete_draft_confirm'): trans('entities.pages_delete_confirm') }}
                        </strong>
                    </p>
                </div> -->
                <div>
                        <div class="form-group text-right">
                            <button type="submit" name="{{ trans('common.encrypt') }}" class="button">{{ trans('common.encrypt') }}</button>
                            <button type="submit" name="{{ trans('common.encrypt') }}" class="button">{{ trans('common.decrypt') }}</button>
                        </div>
                </div>
            </div>
            </form>
        </div>
    </div>

@stop