<template>
  <div class="app-container">
    <el-row :gutter="12">
      <el-col :span="16">
        <el-card shadow="always">
          <el-select
            v-model="selectedproduct"
            clearable
            filterable
            remote
            reserve-keyword
            default-first-option
            placeholder="Start typing or scaning for product"
            :remote-method="getProducts"
            :loading="loading"
            class="selectproduct"
            label="Select Product"
            @change="addCartProduct">
            <el-option
              v-for="product in products"
              :key="product.id"
              :label="product.name"
              :value="product.id"
            >
              <span style="float: left">{{ product.name }}</span>
              <span style="float: left; margin-left:50px; color: #8492a6; font-size: 13px">Code: {{ product.code }}</span>
              <span style="float: right; color: red; font-size: 13px">Price: {{ product.price }}</span>
            </el-option>
          </el-select>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card shadow="always">
          <el-select
            v-model="cart.supplier"
            clearable
            filterable
            remote
            reserve-keyword
            default-first-option
            placeholder="Find Supplier"
            :remote-method="getCustomers"
            :loading="loading">
            <el-option
              v-for="customer in customers"
              :key="customer.id"
              :label="customer.name"
              :value="customer.id"
            />
          </el-select>
          <el-button type="primary" @click="showcustpopup = !showcustpopup"><svg-icon icon-class="add" /></el-button>
        </el-card>
      </el-col>
    </el-row>
    <el-row :gutter="12" class="mt-10">
      <el-col :span="10">
        <el-card shadow="always">
          <el-row :gutter="8">
            <el-col v-for="fp in featured_product" :key="fp.id" :span="8" >
              <el-card shadow="always" class="featurd-product-list" @click.native="addFeaturedCart(fp.id)">
                <div class="producname">{{fp.name}}</div>
                <div class="productprice">Price: {{fp.sale_price}}</div>
                <div class="producnamecode">Code: {{fp.code}}</div>
              </el-card>
            </el-col>
          </el-row>
        </el-card>
      </el-col>
      <el-col :span="14">
        <el-card shadow="always">
          <div slot="header" class="clearfix">
            <div class="tag-group">
              <div class="cartheader first">Qty: </div>
              <el-tag
                effect="dark"
                class="tagheader">
                {{totalQuantity}}
              </el-tag>
              <div class="cartheader">Items: </div>
              <el-tag
                type="success"
                class="tagheader"
                effect="dark">
                {{totalItems}}
              </el-tag>
              <div class="cartheader">Total: </div>
              <el-tag
                type="danger"
                class="tagheader"
                effect="dark">
                {{totalAmount}}
              </el-tag>
            </div>
          </div>
          <el-table :data="cart.products" stripe border>
            <el-table-column label="" :min-width="20">
              <template slot-scope="scope">
                <el-button type="danger" @click="removeCartProduct(scope.row.id)" icon="el-icon-delete" size="mini" circle />
              </template>
            </el-table-column>
            <el-table-column label="Product" :min-width="150">
              <template slot-scope="scope">
                <div class="cart-product">{{ scope.row.name }} <span class="small" :show="scope.row.code">({{scope.row.code}})</span></div>
              </template>
            </el-table-column>
            <el-table-column label="Price" prop="price" :min-width="40" />
            <el-table-column label="Quantity">
              <template slot-scope="scope">
                <el-input-number controls-position="right" v-model="scope.row.quantity" :min="1" size="mini" @change="updateProducts()" />
              </template>
            </el-table-column>
            <el-table-column label="Discount" :min-width="40">
              <template slot-scope="scope">
                <el-input
                  v-model="scope.row.discount"
                  size="mini"
                  placeholder="%"
                  clearable
                />
              </template>
            </el-table-column>
            <el-table-column label="Total">
              <template slot-scope="scope">
                <div class="cart-product">{{ productTotal(scope.row.quantity, scope.row.price, scope.row.discount) }} </div>
              </template>
            </el-table-column>
          </el-table>
          <div class="sale-final-buttons">
            <el-button-group>
              <el-button type="warning" icon="el-icon-plus" @click="holdSale()">Hold Sale</el-button>
              <el-button type="danger" icon="el-icon-close" @click="cancelSale()">Cancel</el-button>
              <el-button type="primary" icon="el-icon-bank-card">Payment</el-button>
              <el-button type="success" icon="el-icon-goods" @click="completePurchase()">Cash</el-button>
            </el-button-group>
          </div>
        </el-card>
      </el-col>
    </el-row>
    <el-dialog title="Add Category" :visible.sync="showcustpopup">
      <el-form ref="customerform" :rules="customeraddrules" :model="addcustomer">
        <el-form-item label="Customer Name" label-width="100" prop="name">
          <el-input v-model="addcustomer.name" autocomplete="off"/>
        </el-form-item>
        <el-form-item label="Phone#" label-width="100">
          <el-input v-model="addcustomer.phone" autocomplete="off"/>
        </el-form-item>
        <el-form-item label="Address" label-width="100">
          <el-input v-model="addcustomer.address" autocomplete="off"/>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showcustpopup = false">Cancel</el-button>
        <el-button type="primary" @click="addCustomer('customerform')">Add</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import Resource from '@/api/resource';
const customer = new Resource('customer');
const prod = new Resource('product');
const purchResorc = new Resource('purchase');
export default {
  name: '',
  components: {},
  directives: { },
  data() {
    return {
      list: null,
      search: '',
      total: 0,
      loading: false,
      showcustpopup: false,
      downloading: false,
      products: '',
      featured_product: '',
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        select: '',
      },
      customers: null,
      addcustomer: {
        name: '',
        phone: '',
        address: '',
      },
      selectedproduct: '',
      cart: {
        supplier: '',
        products: [],
        ttlQuantity: 0,
        ttlItems: 0,
        ttlAmount: 0,
      },
      customeraddrules: {
        name: [
          { required: true, message: 'Please enter customer name', trigger: 'blur' },
          { min: 3, max: 50, message: 'Length should be 3 to 50', trigger: 'blur' }
        ],
      }
    };
  },
  computed: {
    totalQuantity: function() {
      return this.cart.products.map(element => element.quantity).reduce((a, b) => a + b, 0);
    },
    totalItems: function() {
      return this.cart.products.length;
    },
    totalAmount: function() {
      return this.cart.products.map(pro => {
        let total = parseFloat(pro.quantity) * parseFloat(pro.price);
        if (pro.discount) {
          const disc = total * pro.discount / 100;
          total = (total - disc);
        }
        return total;
      }).reduce((a, b) => a + b, 0);
    },
  },
  watch: {
    totalQuantity: function() {
      this.cart.ttlQuantity = this.totalQuantity;
    },
    totalItems: function() {
      this.cart.ttlItems = this.totalItems;
    },
    totalAmount: function() {
      this.cart.ttlAmount = this.totalAmount;
    },
  },
  created() {
    this.getFeaturedProducts();
  },
  methods: {
    updateProducts() {
      this.refreshCart();
    },
    async getCustomers(query) {
      this.loading = true;
      this.query.keyword = query;
      const { data } = await customer.list(this.query);
      this.customers = data.accounts.data;
      this.loading = false;
    },
    async getProducts(query) {
      this.loading = true;
      this.query.keyword = query;
      this.query.select = 'productonly';
      const { data } = await prod.list(this.query);
      this.products = data.products.data;
      this.loading = false;
    },
    async getFeaturedProducts(query) {
      const { data } = await prod.customlist('featured_product','');
      this.featured_product = data.products;
    },
    addCartProduct(index, $event) {
      let selectedproduct = this.products.filter( product => product.id == index);
      selectedproduct = selectedproduct[0];
      this.cartProducts(selectedproduct);
    },
    addFeaturedCart(index) {
      let selectedproduct = this.featured_product.filter(product => product.id == index);
      selectedproduct = selectedproduct[0]; 
      this.cartProducts(selectedproduct);   
    },
    cartProducts(selectedproduct) {
      const productExist = this.cart.products.findIndex(product => product.id == selectedproduct.id);
      if (productExist >= 0) {
        const newquantity = this.cart.products[productExist].quantity + 1;
        this.$set(this.cart.products[productExist], 'quantity', newquantity);
        this.refreshCart();
      }
      else {        
        selectedproduct.quantity = 1;
        selectedproduct.price = selectedproduct['sale_price'];
        this.cart.products.push(selectedproduct);
        this.selectedproduct = '';
      }   
    },
    refreshCart() {
      this.cart.products = this.cart.products.filter( product => product.id == product.id);
    },
    productTotal(quantity, price, discount) {
      quantity = parseFloat(quantity);
      price = parseFloat(price);
      discount = parseFloat(discount);
      const total = quantity * price;
      if (discount) {
        const discount_count = total * discount / 100;
        return total - discount_count;
      } else {
        return total;
      }
    },
    removeCartProduct(id) {
      this.cart.products = this.cart.products.filter( product => product.id != id);
    },
    cancelSale() {
      if (this.cart.products.length) {
        this.$alert('Are you really want to cancel this sale?', 'Title', {
          confirmButtonText: 'OK',
          callback: action => {
            this.cart.products = [];
            this.$message({
              type: 'success',
              message: 'Sale deleted successfully.',
            });
          }
        });
      }
    },
    async completePurchase() {
      if(this.cart.supplier == '') {
        this.$message({
          message: 'Please enter supllier.',
          type: 'error',
        });
      } else {
        const { data } = await purchResorc.store(this.cart).then( result => {
          this.$message({
            message: 'Purchase added successfully.',
            type: 'success',
          });
          this.cart.products = [];
          this.cart.supplier = '';
        });
      }
    },
    addCustomer(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) { 
          const { data } = customer.store(this.addcustomer).then(result => {
            this.customers = result;
            this.$message({
              message: 'Added Successfully.',
              type: 'success',
            });
            this.showcustpopup = false;
          });
        }
      });
    }
  },
};
</script>
<style  scoped>
.selectproduct {
  width:80%;
}
.mt-10 {
  margin-top: 10px;
}
.cart-product {
  font-weight: bold;
}
.small {
  font-weight: normal;
  font-size:10px;
}
.cartheader {
  font-weight: bold;
  font-size: 17px;
  margin-right: 5px;
  display: inline-block;
  margin-left: 20px;
}
.first {
  margin-left: 0;
  display: inline-block;
}
.tagheader {
  font-weight: bold;
  font-size: 17px;
}
.sale-final-buttons {
  margin-top: 20px;
  text-align: right;
}
.featurd-product-list {
	margin-bottom: 10px;
  cursor: pointer;
}
.producname {
	font-weight: bold;
}
.productprice {
	color: #ed3338;
	font-size: 12px;
}
.producnamecode {
  font-size:12px;
}
</style>
