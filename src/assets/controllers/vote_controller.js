import { Controller } from '@hotwired/stimulus';

function apiFetch(url, options = {}) {
    const token = document.querySelector('meta[name="api-token"]').content;

    return fetch(url, {
        ...options,
        headers: {
            ...options.headers,
            'Authorization': `Bearer ${token}`,
        },
    });
}

export default class extends Controller {
    static targets = ['likeCount', 'dislikeCount', 'likeBtn', 'dislikeBtn'];
    static values = { commentId: Number };

    async vote(event) {
        const type = event.currentTarget.dataset.voteType;
        [this.likeBtnTarget, this.dislikeBtnTarget].forEach(b => b.disabled = true);

        try {
            const response = await apiFetch(`/api/maps/comments/${this.commentIdValue}/vote/${type}`, {
                method: 'POST',
            });

            if (!response.ok) throw new Error('Vote failed');
            const data = await response.json();

            this.likeCountTarget.textContent = data.likes;
            this.dislikeCountTarget.textContent = data.dislikes;

            this.likeBtnTarget.classList.toggle('text-green-300', data.state === 1);
            this.dislikeBtnTarget.classList.toggle('text-red-400', data.state === -1);
        } catch (err) {
            console.error(err);
        } finally {
            [this.likeBtnTarget, this.dislikeBtnTarget].forEach(b => b.disabled = false);
        }
    }
}