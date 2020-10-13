<template>
  <div class="app-container">
    <div class="filter-container">
      <el-date-picker
        v-model="query.daterange"
        type="daterange"
        align="right"
        unlink-panels
        range-separator="To"
        start-placeholder="Start date"
        end-placeholder="End date"
        :picker-options="pickerOptions"
        format="dd/MM/yyyy"
        value-format="yyyy-MM-dd"
        style="width:415px"
      />
      <el-button v-waves class="" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
    </div>
    <el-table
      :data="list"
      stripe
      border
      style="width: 100%"
    >
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-table :data="props.row.products" border stripe>
            <el-table-column label="Product" prop="product.name" />
            <el-table-column label="Qty" prop="quantity" />
            <el-table-column label="Price" prop="price" />
            <el-table-column label="Discount" prop="discount" />
            <el-table-column label="Total">
              <template slot-scope="props">
                {{perProductSum(props.row.quantity, props.row.price, props.row.discount)}}
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>
      <el-table-column label="Date">
        <template slot-scope="props">
          <p>{{props.row.created_at | dateformat}}</p>
        </template>
      </el-table-column>
      <el-table-column
        label="Customer"
        prop="customer.name" />
      <el-table-column
        label="Total"
        prop="total" />
      <el-table-column
        label="Quantity"
        prop="quantity" />
      <el-table-column
        label="Items"
        prop="total_items" />
      <el-table-column
        label="Discount"
        prop="discount" />
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
  </div>
</template>
<script>
import Pagination from '@/components/Pagination';
import Resource from '@/api/resource';
import moment from 'moment'
const saleReso = new Resource('sale');
export default {
  name: '',
  components: { Pagination },
  directives: { },
  filters: {
    dateformat: (date) => {
      return (!date) ? '' : moment(date).format('DD MMM, YYYY');
    },
  },
  data() {
    return {
      list: null,
      search: '',
      total: 0,
      loading: true,
      downloading: false,
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          },
        }],
      },
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        daterange: '',
        role: '',
      },
    };
  },
  computed: {
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const { data } = await saleReso.list(this.query);
      this.list = data.sales.data;
      this.total = data.sales.total;
    },
    perProductSum(qty, price, disc) {
      if (disc){
        const total = parseFloat(qty)*parseFloat(price);
        const discount = total*disc/100;
        return total - discount;
      } else {
        return qty * price;
      }
    },
    handleFilter() {
      this.getList()
    },
  },
};
</script>
<style  scoped>
</style>
