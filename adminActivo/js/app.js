const storeAPP = {
   
    state: {
        datos:{
           
        }
    },
    setData:function(valor){
        this.state.datos = {};
        this.state.datos = valor;
    },
    getData:function(){
        return this.state.datos;
    }
};

const store = new Vuex.Store({
        state: {
            flavor: ''
        },
        mutations: {
            change(state, flavor) {
                state.flavor = flavor
            }
        },
        getters: {
            flavor: state => state.flavor
        }
});

const router = new VueRouter({
    routes: routes
});

const app = new Vue({
	
	  created: function () {
    CONFIG.TOKEN = localStorage.getItem("TOKEN");

  },
    
    router: router,
    store
}).$mount("#app");


