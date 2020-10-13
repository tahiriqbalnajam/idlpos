import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/articles',
    method: 'get',
    params: query,
  });
}
export function fetchListc(query) {
  return request({
    url: '/customer',
    method: 'get',
    params: query,
  });
}
export function search_outlet(query) {
  return request({
    url: '/search_outlet',
    method: 'get',
    params: { query },
  });
}
export function fetchArticle(id) {
  return request({
    url: '/articles/' + id,
    method: 'get',
  });
}

export function fetchPv(id) {
  return request({
    url: '/articles/' + id + '/pageviews',
    method: 'get',
  });
}

export function createArticle(data) {
  return request({
    url: '/article/create',
    method: 'post',
    data,
  });
}

export function updateArticle(data) {
  return request({
    url: '/article/update',
    method: 'post',
    data,
  });
}
