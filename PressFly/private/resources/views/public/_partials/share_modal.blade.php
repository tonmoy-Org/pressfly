<?php
/**
 * @var \App\Models\Article $article
 */

?>
@push('footer')
    <div class="modal fade" id="shareModal" tabindex="-1"
         aria-hidden="true" style="overflow: visible">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Share this') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('public._partials.share', ['article' => $article])
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.article-share-button').on('click', function (e) {
            e.preventDefault();

            if (navigator.share) {
                navigator.share({
                    title: '{{ $article->title }}',
                    url: '{{ $article->permalink() }}'
                }).then(() => {
                }).catch(console.error);
            } else {
                $('#shareModal').modal();
            }
        });
    </script>
@endpush
