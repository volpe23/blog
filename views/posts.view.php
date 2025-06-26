<div class="mx-auto">
    <?php
    if (auth()->check()) view("post_create") ?>
    <?php if (isset($posts)): ?>
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <a href="<?= route("posts.show", ["id" => $post->id]) ?>">
                    <div class="flex flex-col gap-4 rounded border border-gray-200 shadow bg-gray-300">
                        <h3><?= $post->title ?></h3>
                        <p><?= $post->text ?></p>
                        <span>Created <?= timestamp($post->created_at) ?></span>
                        <p><?= $post->user()->username ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts are created</p>
        <?php endif; ?>
    <?php endif; ?>
</div>