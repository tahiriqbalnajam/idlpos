<template>
  <div class="app-container">
    <el-row>
      <el-button type="primary" @click="addoutlet">Add Outlet</el-button>
    </el-row>
    <el-table
      :data="list"
      style="width: 100%"
      :loading="loading"
    >
      <el-table-column label="ID" prop="id" />
      <el-table-column label="Name" prop="name" />
      <el-table-column label="Status">
        <el-switch v-model="status" active-value="true" inactive-value="false"  />
      </el-table-column>
      <el-table-column align="right">
        <template slot="header" slot-scope="scope">
          <el-input ref="search" v-model="search" size="mini" placeholder="Type to search" :onkeypress="search_data()" />
        </template>
        <template slot-scope="scope">
          <el-button
            size="mini"
            @click="handledit(scope.row.id, scope.row.name)"
          >Edit</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(scope.row.id, scope.row.name)"
          >Delete</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog :title="title" :visible.sync="outletForm">
      <div class="form-container">
        <el-form
          ref="outlet"
          :model="outlet"
          :rules="rules"
          label-position="left"
          label-width="150px"
          style="max-width: 500px;"
        >
          <el-form-item label="Name" prop="name">
            <el-input v-model="outlet.name" />
          </el-form-item>
          <el-form-item label="Status" prop="status">
            <el-switch v-model="outlet.status" active-value="true" inactive-value="false" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="outletForm = false">
            Cancel
          </el-button>
          <el-button type="primary" @click="handleSubmit('outlet')">
            Confirm
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import Resource from '@/api/resource';
import { search_outlet } from '@/api/article';
const outletResource = new Resource('outlet');
export default {
  name: 'Outlet',
  data() {
    var name = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Please input the Outlet Name'));
      } else {
        callback();
      }
    };
    return {
      list: [],
      loading: false,
      outletForm: false,
      search: '',
      title: '',
      value: '',
      datum: '',
      active: 'true',
      inactive: 'false',
      outlet: {
        name: '',
        status: true,
      },
      total: 0,
      listQuery: {
        page: 1,
        limit: 4,
        importance: undefined,
        title: undefined,
        type: undefined,
      },
      rules: {
        name: [{ validator: name, trigger: 'blur' }],
      },

    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.loading = true;
      const { data } = await outletResource.list({});
      this.list = data.outlets;
      this.loading = false;
    },
    addoutlet(){
      this.title = 'Add New Outlet';
      this.outletForm = true;
      this.outlet = {
        name: '',
        status: '',
      };
    },
    handleSubmit(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.outlet.id !== undefined) {
            outletResource
              .update(this.outlet.id, this.outlet)
              .then(response => {
                this.$message({
                  type: 'success',
                  message: 'Outlet info has been updated successfully',
                  duration: 5 * 1000,
                });
                this.getList();
              })
              .catch(error => {
                console.log(error);
              })
              .finally(() => {
                this.outletForm = false;
              });
          } else {
            outletResource
              .store(this.outlet)
              .then(response => {
                this.$message({
                  message:
                    'New Outlet' +
                    this.outlet.name +
                    ' has been created successfully.',
                  type: 'success',
                  duration: 5 * 1000,
                });
                this.outlet = {
                  name: '',
                  status: '',
                };
                this.outletForm = false;
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
        'This will permanently delete Outlet ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          outletResource
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
    handledit(id, name){
      this.title = 'Edit  ' + name + '  Outlet';
      this.outlet = this.list.find(outlets => outlets.id === id);
      console.log(this.outlet);
      this.outletForm = true;
    },
    async search_data(){
      this.list = [];
      this.datum = await search_outlet(this.$refs.search.value);
      console.log(this.datum);
      this.list = this.datum.data.result;
    },
  },
};
</script>
