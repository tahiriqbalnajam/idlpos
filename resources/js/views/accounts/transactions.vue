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
    <el-row :gutter="12">
      <el-col :span="12">
        <h1>Naam</h1>
        <el-card shadow="always">
          <el-table
            :data="list"
            style="width: 100%"
          >
            <el-table-column label="Date">
              <template slot-scope="props">
                {{ props.row.created_at | dateformat }}
              </template>
            </el-table-column>
            <el-table-column label="Name" prop="account.name" />
            <el-table-column label="Amount" prop="amount" />
            <el-table-column align="right">
              <template slot="header" slot-scope="scope">
                <el-input ref="search" v-model="search" size="mini" placeholder="Type to search" :onkeypress="search_data()" />
              </template>
              <template slot-scope="scope">
                <el-button
                  size="mini"
                  class="el-icon-edit"
                  @click="handleEdit(scope.row.id, scope.row.name)"
                />
                <el-button
                  size="mini"
                  type="danger"
                  class="el-icon-delete"
                  @click="handleDelete(scope.row.id, scope.row.name)"
                />
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
      <el-col :span="12">
        <h1>Jama</h1>
        <el-card shadow="always">
          <el-table
            :data="jama"
            style="width: 100%"
          >
            <el-table-column label="Date">
              <template slot-scope="props">
                {{ props.row.created_at | dateformat }}
              </template>
            </el-table-column>
            <el-table-column label="Name" prop="account.name" />
            <el-table-column label="Amount" prop="amount" />
            <el-table-column align="right">
              <template slot="header" slot-scope="scope">
                <el-input ref="search" v-model="search" size="mini" placeholder="Type to search" :onkeypress="search_data()" />
              </template>
              <template slot-scope="scope">
                <el-button
                  size="mini"
                  class="el-icon-edit"
                  @click="handleEdit(scope.row.id, scope.row.name)"
                />
                <el-button
                  size="mini"
                  type="danger"
                  class="el-icon-delete"
                  @click="handleDelete(scope.row.id, scope.row.name)"
                />
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>
    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
  </div>
</template>
<script>
import Pagination from '@/components/Pagination';
import Resource from '@/api/resource';
import moment from 'moment';
const transResor = new Resource('transaction');
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
      jama: null,
      search: '',
      total: 0,
      loading: true,
      downloading: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        daterange: '',
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
      const { data } = await transResor.list(this.query);
      this.list = data.transactions;
      this.jama = data.transaction_jama;
      this.total = data.transactions.total;
    },
    handleFilter() {
      this.getList();
    },
  },
};
</script>
<style  scoped>
</style>