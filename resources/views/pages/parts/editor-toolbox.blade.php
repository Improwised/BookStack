<div component="editor-toolbox" class="floating-toolbox">

    <div class="tabs flex-container-column justify-flex-start">
        <div class="tabs-inner flex-container-column justify-center">
            <button type="button" refs="editor-toolbox@toggle" title="{{ trans('entities.toggle_sidebar') }}" aria-expanded="false" class="toolbox-toggle">@icon('caret-left-circle')</button>
            <button type="button" refs="editor-toolbox@tab-button" data-tab="tags" title="{{ trans('entities.page_tags') }}" class="active">@icon('tag')</button>
            @if(userCan('attachment-create-all'))
                <button type="button" refs="editor-toolbox@tab-button" data-tab="files" title="{{ trans('entities.attachments') }}">@icon('attach')</button>
            @endif
            <button type="button" refs="editor-toolbox@tab-button" data-tab="templates" title="{{ trans('entities.templates') }}">@icon('template')</button>
            @if($comments->enabled())
                <button type="button" refs="editor-toolbox@tab-button" data-tab="comments" title="{{ trans('entities.comments') }}">@icon('comment')</button>
            @endif
            <button type="button" refs="editor-toolbox@tab-button" data-tab="cover_image" title="{{ trans('common.cover_image') }}">@icon('image')</button>
        </div>
    </div>

    <div refs="editor-toolbox@tab-content" data-tab-content="tags" class="toolbox-tab-content">
        <h4>{{ trans('entities.page_tags') }}</h4>
        <div class="px-l">
            @include('entities.tag-manager', ['entity' => $page])
        </div>
    </div>

    @if(userCan('attachment-create-all'))
        @include('attachments.manager', ['page' => $page])
    @endif

    <div refs="editor-toolbox@tab-content" data-tab-content="templates" class="toolbox-tab-content">
        <h4>{{ trans('entities.templates') }}</h4>

        <div class="px-l">
            @include('pages.parts.template-manager', ['page' => $page, 'templates' => $templates])
        </div>
    </div>

    @if($comments->enabled())
        @include('pages.parts.toolbox-comments')
    @endif

    <div refs="editor-toolbox@tab-content" data-tab-content="cover_image" class="toolbox-tab-content">
        <h4>{{ trans('common.cover_image') }}</h4>
        <div class="px-l">
                <p class="small">{{ trans('common.cover_image_description') }}</p>

                @include('form.image-picker', [
                    'defaultImage' => url('/book_default_cover.png'),
                    'currentImage' => (isset($page) && $page->cover) ? $page->getPageCover() : url('/book_default_cover.png') ,
                    'name' => 'image',
                    'imageClass' => 'cover'
                ])
        </div>
    </div>

</div>
