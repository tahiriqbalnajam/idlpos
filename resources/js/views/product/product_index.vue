<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-plus" @click="handleCreate">
        Add Product
      </el-button>
    </div>
    <el-table :data="list" style="width: 100%">
      <el-table-column label="ID" prop="id" width="50px"/>
      <el-table-column label="Code" prop="code" />
      <el-table-column label="Name" prop="name" />
      <el-table-column label="Category" prop="category.title" />
      <el-table-column label="P Price" prop="purchase_price" />
      <el-table-column label="S Price" prop="sale_price" />
      <el-table-column label="W Price" prop="wholesale_price" />
      <el-table-column label="Status">
        <template slot-scope="scope">
          <el-badge is-dot class="badge" :type="scope.row.status == 'enable' ? 'primary' : ''">{{ scope.row.status }}</el-badge>
        </template>
      </el-table-column>
      <el-table-column align="right" fixed="right" >
        <template slot="header" slot-scope="scope">
          <el-input ref="search" v-model="search" size="mini" placeholder="Type to search" :onkeypress="search_data()" />
        </template>
        <template slot-scope="scope">
          <el-tooltip content="Stock" placement="top">
            <el-button
              size="mini"
              @click="handleStock(scope.row.id, scope.row.name)"
            ><svg-icon icon-class="stock" /></el-button>
          </el-tooltip>
          
          <el-button
            size="mini"
            type="danger"
            @click="handleEdit(scope.row.id, scope.row.name)"
          ><svg-icon icon-class="edit" /></el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
    <el-drawer title="Manage Stock" :visible.sync="showstock" size="70%" class="idldrawer">
      <el-form :inline="true" :rules="stockrules" ref="stockForm" :model="stock" size="mini" :loading="stock_loading">
        <el-form-item label="Quantity" prop="stockquantity">
          <el-input-number v-model="stock.quantity" />
        </el-form-item>
        <el-form-item label="Remarks" prop="remarks">
          <el-input v-model="stock.remarks" clearable/>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="addStock('stockForm')">Add Stock</el-button>
        </el-form-item>
      </el-form>
      <el-divider />
      <el-table :data="stocklist" style="width: 100%">
        <el-table-column label="Data Tracking" prop="created_at" />
        <el-table-column label="Employee" prop="user_id" />
        <el-table-column label="In/Out Qty" prop="quantity" />
        <el-table-column label="Remarks" prop="remarks" />
      </el-table>
    </el-drawer>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination';
import moment from 'moment';
import Resource from '@/api/resource';
import { getStock }from '@/api/product';
import { addStock }from '@/api/product';
const addPro = new Resource('product');
export default {
  name: '',
  components: { Pagination },
  directives: {  },
  data() {
    return {
      list: null,
      total: 0,
      loading: true,
      search: '',
      showstock: false,
      downloading: false,
      query: {
        page: 1,
        limit: 10,
        keyword: '',
        role: '',
      },
      stock_loading: false,
      stocklist: null,
      stock: {
        outlet_id: '1',
        product_id: '',
        quantity: '',
        remarks: '',
      },
      stockrules:{
       
      },
      drawer: {
        direction: 'left',
      }
    };
  },
  computed: {
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const { data } = await addPro.list(this.query);
      this.list = data.products.data;
      this.total = data.products.total
    },
    handleCreate() {
      this.$router.push({path:'create'})
    },
    handleEdit(id, name) {
      this.$router.push({path:'edit/'+id})
    },
    async search_data() {
      const { data } = await addPro.list({});
    },
    async handleStock(id, name) {
      this.showstock = true;
      this.stock.product_id = id;
      const { data } = await getStock(id);
      this.stocklist = data.stock;

    },
    async addStock(formName) {
      await addStock(this.stock);
      this.stock.quantity = 0;
      this.stock.remarks = '';
      const { data } =  await getStock(this.stock.product_id);
      this.stocklist = data.stock;
    },
  },
};
</script>
<style>
  .el-drawer__body {
    padding:0 20px;
  }
  .badge {
    top:4px;
  }
</style>
<style  scoped lang="scss">
</style>
