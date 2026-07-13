document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.vote-btn');
    if (!btn) return;

    const container = btn.closest('[data-comment-votes]');
    const commentId = container.dataset.commentId;
    const type = btn.dataset.voteType;
    const csrfToken = document.body.dataset.csrfToken;

    // Disable both buttons in this group while the request is in flight
    const buttons = container.querySelectorAll('.vote-btn');
    buttons.forEach(b => b.disabled = true);

    try {
        const response = await fetch(`/maps/comments/${commentId}/vote/${type}`, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken,
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Vote failed');
        }

        const data = await response.json();

        container.querySelector('[data-like-count]').textContent = data.likes;
        container.querySelector('[data-dislike-count]').textContent = data.dislikes;

        buttons.forEach(b => b.classList.remove('text-green-300'));
        buttons.forEach(b => b.classList.remove('text-red-400'));
        if (data.state === 1) {
            container.querySelector('[data-vote-type="like"]').classList.add('text-green-300');
        } else if (data.state === -1) {
            container.querySelector('[data-vote-type="dislike"]').classList.add('text-red-400');
        }
    } catch (err) {
        console.error(err);
        // optionally show a toast/error state here
    } finally {
        buttons.forEach(b => b.disabled = false);
    }
});