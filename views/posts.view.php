<div class="md:max-w-3xl mx-auto">
    <?php

    use Core\Auth;

    if (Auth::check()) view("post_create") ?>
    <?php if (isset($posts)): ?>
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <?= var_dump($post); ?>
                <div class="flex flex-col gap-2 rounded border border-gray-200 shadow bg-gray-300">
                    <h3><?= $post->title ?></h3>
                    <p><?= $post->text ?></p>
                    <span>Created <?= time() - $post->created_at ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts are created</p>
        <?php endif; ?>
    <?php endif; ?>
</div>