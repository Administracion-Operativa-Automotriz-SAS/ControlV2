



const LayoutWeb = httpVueLoader("./src/layouts/web.php");
const dashboard = httpVueLoader("./src/pages/home/dashboard.php");
const home = httpVueLoader("./src/pages/home/home.php");

const loginSI =httpVueLoader("./src/layouts/login.vue");
const login =httpVueLoader("./src/layouts/index.php");
const ingresar =httpVueLoader("./src/layouts/ingresar.php");
const nuevo =httpVueLoader("./src/layouts/nuevo.php");
const contacto =httpVueLoader("./src/layouts/contacto.php");
const servicios =httpVueLoader("./src/layouts/servicios.php");
const acerca =httpVueLoader("./src/layouts/acerca.php");



const VueRecaptcha = httpVueLoader("./src/components/base/captcha.vue");
const mis_novedades = httpVueLoader("./src/pages/novedad/mis_novedades.php");

const routes = [
    { path: '/', component: LayoutWeb, props: true,

        children: [
            {
                path: '/',
                component: dashboard
            }
        ]
    },
	{ path: '/', component: loginSI, props: true,

      children: [
            {
                 name:'/loginSI',
            path: '/loginSI',
            }
        ]
    },
	{ path: '/', component: login, props: true,

      children: [
            {
                 name:'/login',
            path: '/login',
            }
        ]
    },
	{ path: '/', component: login, props: true,

      children: [
            {
                 name:'/nuevo',
            path: '/nuevo',
            }
        ]
    },
	{ path: '/', component: acerca, props: true,

      children: [
            {
                 name:'/acerca',
            path: '/acerca',
            }
        ]
    },
	{ path: '/', component: servicios, props: true,

      children: [
            {
                 name:'/servicios',
            path: '/servicios',
            }
        ]
    },
	{ path: '/', component: ingresar, props: true,

      children: [
            {
                 name:'/ingresar',
            path: '/ingresar',
            }
        ]
    },
	{ path: '/', component: contacto, props: true,

      children: [
            {
                 name:'/contacto',
            path: '/contacto',
            }
        ]
    },

    { path: '/home', component: LayoutWeb, props: true,
    children: [
        {
            name:'/home',
            path: '/home',
            component: home
        }
       ]
    },
 { path: '/dashboard', component: LayoutWeb, props: true,
    children: [
        {
            name:'/dashboard',
            path: '/dashboard',
            component: dashboard
        }
       ]
    },
   

{ path: '/mis_novedades', component: LayoutWeb, props: true,
    children: [
        {
            name:'/mis_novedades',
            path: '/mis_novedades',
            component: mis_novedades
        }
       ]
    },
    
];