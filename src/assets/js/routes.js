export const routes = {
  beatmapsetShow: (id) => `/maps/${id}`,
  userShow: (id) => `/users/${id}`,
}

export const apiRoutes = {
  commentVote: (id, type) => `api/maps/comments/${id}/vote/${type}`,
}