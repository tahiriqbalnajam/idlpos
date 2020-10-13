import request from '@/utils/request';

export function getStock(id) {
  return request({
    url: 'get_stock/' + id,
    method: 'get',
  });
}

export function addStock(query) {
  return request({
    url: '/add_stock',
    method: 'post',
    data: query,
  });
}

export function addVisitMedicines(medicines) {
  return request({
    url: '/add_visit_medicines',
    method: 'post',
    data: medicines,
  });
}

export function endVisit(visitid) {
  return request({
    url: 'end_visit/' + visitid,
    method: 'get',
  });
}

export function deleteVisitMedicines(visitid, medid) {
  return request({
    url: 'delete_visit_medicine/' + visitid + '/' + medid,
    method: 'get',
  });
}
export function saveVisitTest(id, patient_id, visitid){
  return request({
    url: 'saveVisitTest/' + id + '/' + patient_id + '/' + visitid,
    method: 'post',
  });
}
export function getPatienttest(){
  return request({
    url: 'getVisitTest/',
    method: 'get',
  });
}
export function deleteTest(id) {
  return request({
    url: 'deleteTest/' + id,
    method: 'get',
  });
}
export function searchPatient(query) {
  return request({
    url: '/search_patient',
    method: 'post',
    data: query,
  });
}

