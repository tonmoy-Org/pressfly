<?php
/**
 * @var \App\Models\Article $article
 */
?>
<div class='article-share'>
    <a class='share-btn share-btn-facebook'
       href='https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($article->permalink()) ?>'
       rel='nofollow' target='_blank'>
        <i class='fab fa-facebook-f'></i> <span>Facebook</span>
    </a>
    <a class='share-btn share-btn-twitter'
       href='https://twitter.com/intent/tweet?text=<?= urlencode(
           $article->title
       ) ?>&amp;url=<?= urlencode($article->permalink()) ?>'
       rel='nofollow' target='_blank'>
        <i class='fab fa-twitter'></i> <span>Twitter</span>
    </a>
    <a class='share-btn share-btn-whatsapp'
       href='https://api.whatsapp.com/send?text=<?= urlencode($article->title .' '. $article->permalink()) ?>'
       rel='nofollow' target='_blank'>
        <i class="fab fa-whatsapp"></i> <span>WhatsApp</span>
    </a>
    <a class='share-btn share-btn-telegram'
       href='https://telegram.me/share/url?url=<?= urlencode($article->permalink()) ?>&text=<?= urlencode($article->title) ?>'
       rel='nofollow' target='_blank'>
        <i class="fab fa-telegram-plane"></i> <span>Telegram</span>
    </a>
    <a class='share-btn share-btn-linkedin'
       href='https://www.linkedin.com/cws/share?url=<?= urlencode($article->permalink()) ?>'
       rel='nofollow' target='_blank'>
        <i class="fab fa-linkedin-in"></i> <span>Linkedin</span>
    </a>
    <a class='share-btn share-btn-pinterest'
       href='https://pinterest.com/pin/create/button/?url=<?= urlencode(
           $article->permalink()
       ) ?>&amp;media=<?= urlencode(
           asset($article->getMainImage('large'))
       ) ?>&amp;description=<?= urlencode($article->title) ?>'
       rel='nofollow' target='_blank'>
        <i class="fab fa-pinterest"></i> <span>Pinterest</span>
    </a>
    <a class='share-btn share-btn-reddit'
       href='https://www.reddit.com/submit?url=<?= urlencode(
           $article->permalink()
       ) ?>&amp;title=<?= urlencode($article->title) ?>'
       rel='nofollow' target='_blank'>
        <i class="fab fa-reddit-alien"></i> <span>reddit</span>
    </a>
    <a class='share-btn share-btn-vk'
       href='https://vk.com/share.php?url=<?= urlencode(
           $article->permalink()
       ) ?>&amp;title=<?= urlencode($article->title) ?>'
       rel='nofollow' target='_blank'>
        <i class="fab fa-vk"></i> <span>VK</span>
    </a>
    <a class='share-btn share-btn-mail'
       href='mailto:?subject={{ $article->title }}&amp;body={{ $article->permalink() }}'
       rel='nofollow' target='_blank' title='via email'>
        <i class="far fa-envelope"></i> <span>Email</span>
    </a>
</div>
