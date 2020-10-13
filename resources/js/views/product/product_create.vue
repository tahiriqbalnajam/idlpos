<template>
  <div class="app-container">
    <el-header>Add Product</el-header>
    <el-form :rules="rules" ref="productform" :model="product" label-width="170px" size="mini" :loading="loading">
      <el-row :gutter="30">
        <el-col :span="8">
          <el-card class="box-card">
            <div slot="header" class="clearfix">
              <span>Product Info</span>
            </div>
            <el-form-item label="Product Name" prop="name">
              <el-input v-model="product.name" clearable/>
            </el-form-item>
            <el-form-item label="Product Category" prop="name">
              <el-select
                v-model="product.category_id"
                filterable
                remote
                reserve-keyword
                placeholder="Please enter a keyword"
                :remote-method="getlist"
                :loading="loading">
                <el-option
                  v-for="category in categories"
                  :key="category.id"
                  :label="category.title"
                  :value="category.id" 
                />
              </el-select>
              <el-button @click="showCatPopup()"><svg-icon icon-class="add" /></el-button>
            </el-form-item>
            <el-form-item label="Product ID">
              <el-input v-model="product.code" clearable />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="onSubmit('productform')" :loading="loading"><span v-if="product.id == ''">Create</span><span v-else>Update</span></el-button>
              <el-button>Cancel</el-button>
            </el-form-item>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card class="box-card">
            <div slot="header" class="clearfix">
              <span>Price</span>
            </div>
            <el-form-item label="Purchase Price" prop="purchase_price">
              <el-input v-model="product.purchase_price" type="number" step="any" min="0"  />
            </el-form-item>
            <el-form-item label="Sale Price" prop="sale_price">
              <el-input v-model="product.sale_price" type="number" step="any" min="0"  />
            </el-form-item>
            <el-form-item label="Wholesale Price">
              <el-input v-model="product.wholesale_price" type="money"  />
            </el-form-item>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card class="box-card">
            <div slot="header" class="clearfix">
              <span>Inventory</span>
            </div>
            <el-form-item label="Quantity" prop="quantity">
              <el-input v-model="product.quantity" type="number" step="any" />
            </el-form-item>
            <el-form-item label="Reorder Level">
              <el-input v-model="product.reorder" clearable />
            </el-form-item>
          </el-card>
        </el-col>
      </el-row>
    </el-form>
    <el-dialog title="Add Category" :visible.sync="showcategorypopup">
      <el-form :model="category">
        <el-form-item label="Category Title" label-width="100">
          <el-input v-model="category.title" autocomplete="off"/>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showcategorypopup = false">Cancel</el-button>
        <el-button type="primary" @click="addCategory()">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import Resource from '@/api/resource';
const addPro = new Resource('product');
const category = new Resource('category');
import { deleteTest } from '@/api/product';
export default {
  name: '',
  components: {},
  directives: {},
  loading: false,
  data() {
    return {
      list: null,
      total: 0,
      loading: false,
      downloading: false,
      showcategorypopup: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
      },
      product: {
        id: '',
        name: '',
        uid: '',
        purchase_price: '',
        sale_price: '',
        wholesale_price: '',
        quantity: 1,
        reorder: 1,
        category_id: '',
      },
      category: {
        title:'',
      },
      categories: null,
      rules: {
        name: [
          { required: true, message: 'Please enter product name', trigger: 'blur' },
          { min: 3, max: 50, message: 'Length should be 3 to 50', trigger: 'blur' }
        ],
        purchase_price: [
          {  required: true, message: 'Please enter product name', trigger: 'blur' },
        ],
        sale_price: [
          { required: true, message: 'Please enter product name', trigger: 'blur' },
        ],
        quantity: [
          { required: true, message: 'Please enter product name', trigger: 'blur' },
        ],
      }
    };
  },
  computed: {},
  created() {
    if ( this.$route.params && this.$route.params.id) {
      this.product.id = this.$route.params.id;
      this.showinventory = false;
      this.getProduct(this.product.id);
    }
      
  },
  methods: {
    getProduct(id) {
      const { data } = addPro.get(id).then( result => {
        this.product = result;
      })
    },
    async getlist(query) {
      this.loading = true;
      const { data } = await category.list(query);
      this.categories = data.categories.data;
      this.loading = false;
    },
    showCatPopup() {
      this.showcategorypopup = true;
    },
    async addCategory() {
      const {data} = category.store(this.category).then(result => {
        this.$message({
          message: 'Category added Successfully.',
          type: 'success',
        });
        this.showcategorypopup = false;
      });
    },
    onSubmit(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.loading = true;
          if (this.product.id != '') {
            const { data } = addPro.update(this.product.id, this.product).then(result => {
              this.product = result;
              this.loading = false;
              this.$message({
                message: 'Updated Successfully.',
                type: 'success',
              });
            });   
          }
          else {
            const { data } = addPro.store(this.product).then(result => {
              this.product = result;
              this.loading = false;
              this.$message({
                message: 'Added Successfully.',
                type: 'success',
              });
            });
          }
          
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },    
  },
};
</script>
<style scoped>
label {
  font-weight: normal;
}
</style>
