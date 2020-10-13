import request from '@/utils/request';

export function fetchListc(query) {
  return request({
    url: '/customer',
    method: 'get',
    params: query,
  });
}
export function search(query) {
  return request({
    url: '/search',
    method: 'get',
    params: { query },
  });
}

