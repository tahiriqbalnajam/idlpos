<template>
  <div class="app-container">
    <el-row>
      <el-button type="primary" plain @click="addCustomer">Add Customer</el-button>
      <el-button
        v-waves
        :loading="downloadLoading"
        class="filter-item"
        type="primary"
        icon="el-icon-download"
        @click="handleDownload"
      >
        {{ $t('table.export') }}
      </el-button>
    </el-row>
    <el-table
      :data="list"
      style="width: 100%"
    >
      <el-table-column label="ID" prop="id" />
      <el-table-column label="Name" prop="name" />
      <el-table-column label="Type" prop="account_type.title" />
      <el-table-column label="Phone" prop="phone" />
      <el-table-column label="Address" prop="address" />
      <el-table-column align="right">
        <template slot="header" slot-scope="scope">
          <el-input ref="search" v-model="search" size="mini" placeholder="Type to search" :onkeypress="search_data()" :debounce="400"/>
        </template>
        <template slot-scope="scope">
          <el-button
            size="mini"
            @click="handleEdit(scope.row.id, scope.row.name)"
          >Edit</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(scope.row.id, scope.row.name)"
          >Delete</el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
    <el-dialog :title="title" :visible.sync="customerForm">
      <div class="form-container">
        <el-form
          ref="account"
          :model="account"
          :rules="rules"
          label-position="left"
          label-width="150px"
          style="max-width: 500px;"
        >
          <el-form-item label="Name" prop="name">
            <el-input v-model="account.name" />
          </el-form-item>
          <el-form-item label="Phone" prop="phone">
            <el-input v-model="account.phone" />
          </el-form-item>
          <el-form-item label="Account Type">
            <el-select v-model="account.type" placeholder="Account Type">
              <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Address" prop="address">
            <el-input v-model="account.address" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="customerForm = false">
            Cancel
          </el-button>
          <el-button type="primary" @click="handleSubmit('account')">
            Confirm
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { fetchListc } from '@/api/customer';
import { search } from '@/api/customer';
import Resource from '@/api/resource';
const customerResource = new Resource('customer');
import waves from '@/directive/waves';
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination';
export default {
  name: 'Customer',
  directives: { waves },
  components: { Pagination },
  data() {
    var name = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Please input the Name'));
      } else {
        callback();
      }
    };
    var phone = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Phone Number Cannot Blank'));
      } else {
        callback();
      }
    };
    var address = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Address Cannot Blank'));
      } else {
        callback();
      }
    };
    return {
      list: [],
      total: 0,
      loading: true,
      customerForm: false,
      listQuery: {
        page: 1,
        limit: 4,
        importance: undefined,
        title: undefined,
        type: undefined,
      },
      account: {
        name: '',
        type: '',
        phone: '',
        address: '',
        status: '',
        title: '',
      },
      options: [{
        value: 1,
        label: 'Customer',
      }, {
        value: 2,
        label: 'Supplier',
      }, {
        value: 3,
        label: 'Staff',
      }],
      value: '',
      downloadLoading: false,
      search: '',
      title: '',
      rules: {
        name: [{ validator: name, trigger: 'blur' }],
        phone: [{ validator: phone, trigger: 'blur' }],
        address: [{ validator: address, trigger: 'blur' }],
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.loading = true;
      const { data } = await fetchListc(this.listQuery);
      this.list = data.accounts.data;
      console.log(this.list);
      this.total = data.accounts.total;
      this.loading = false;
    },
    addCustomer() {
      this.customerForm = true;
      this.title = 'Create a New Customer';
      this.account = {
        name: '',
        phone: '',
        address: '',
        type: '',
      };
    },
    handleSubmit(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.account.id !== undefined) {
            customerResource
              .update(this.account.id, this.account)
              .then(response => {
                this.$message({
                  type: 'success',
                  message: 'Customer info has been updated successfully',
                  duration: 5 * 1000,
                });
                this.getList();
              })
              .catch(error => {
                console.log(error);
              })
              .finally(() => {
                this.customerForm = false;
              });
          } else {
            customerResource
              .store(this.account)
              .then(response => {
                this.$message({
                  message:
                    'New Customer  ' +
                    this.account.name +
                    ' has been created successfully.',
                  type: 'success',
                  duration: 5 * 1000,
                });
                this.account = {
                  name: '',
                  phone: '',
                  address: '',
                  type: '',
                };
                this.customerForm = false;
                this.getList();
              })
              .catch(error => {
                console.log(error);
              });
          }
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete Customer ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          customerResource
            .destroy(id)
            .then(response => {
              this.$message({
                type: 'success',
                message: 'Delete completed',
              });
              this.getList();
            })
            .catch(error => {
              console.log(error);
            });
        });
    },
    handleEdit(id, name) {
      this.title = 'Edit Customer ' + name;
      this.account = this.list.find(customers => customers.id === id);
      console.log(this.account);
      this.customerForm = true;
    },
    handleDownload() {
      this.downloadLoading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['id', 'name', 'phone', 'address'];
        const filterVal = ['id', 'name', 'phone', 'address'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list',
        });
        this.downloadLoading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v =>
        filterVal.map(j => {
          if (j === 'timestamp') {
            return parseTime(v[j]);
          } else {
            return v[j];
          }
        })
      );
    },
    async search_data(event){
      if (this.$refs.search.value !== ''){
        this.list = [];
        this.datum = await search(this.$refs.search.value);
        console.log(this.datum);
        this.list = this.datum.data.data;
      }
    },
  },
};
</script>
