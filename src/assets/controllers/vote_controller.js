import { Controller } from '@hotwired/stimulus';

async function apiFetch(url, options = {}) {
    let token = document.querySelector('meta[name="api-token"]').content;

    let response = await fetch(url, {
        ...options,
        headers: { ...options.headers, 'Authorization': `Bearer ${token}` },
    });

    if (response.status === 401) {
        // session might still be valid — try to mint a fresh token
        const refreshResponse = await fetch('/auth/api-token');
        if (refreshResponse.ok) {
            const data = await refreshResponse.json();
            document.querySelector('meta[name="api-token"]').content = data.token;

            response = await fetch(url, {
                ...options,
                headers: { ...options.headers, 'Authorization': `Bearer ${data.token}` },
            });
        }
    }

    return response;
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