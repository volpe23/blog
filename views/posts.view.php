<div class="mx-auto">
    <?php

    use Core\Facades\Auth;

    if (Auth::check()) view("post_create") ?>
    <?php if (isset($posts)): ?>
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="flex flex-col gap-2 rounded border border-gray-200 shadow bg-gray-300">
                    <h3><?= $post->title ?></h3>
                    <p><?= $post->text ?></p>
                    <span>Created <?= timestamp($post->created_at) ?></span>
                    <!-- <span>Created <?= $post->created_at ?></span> -->
                    <p><?= $post->user()->username ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts are created</p>
        <?php endif; ?>
    <?php endif; ?>
</div>