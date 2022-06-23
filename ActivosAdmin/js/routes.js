const LayoutWeb = httpVueLoader("./src/layouts/web.php");
const LayouthHome = httpVueLoader("./src/layouts/home/home.php");
const dashboard = httpVueLoader("./src/pages/home/dashboard.php");
const home = httpVueLoader("./src/pages/home/home.php");
const login = httpVueLoader("./src/layouts/home/login.vue");
const inicio = httpVueLoader("./src/layouts/home/index.php");
const ingresar = httpVueLoader("./src/layouts/home/ingresar.php");
const nuevo = httpVueLoader("./src/layouts/home/nuevo.php");
const contacto = httpVueLoader("./src/layouts/home/contacto.php");
const servicios = httpVueLoader("./src/layouts/home/servicios.php");
const acerca = httpVueLoader("./src/layouts/home/acerca.php");
const VueRecaptcha = httpVueLoader("./src/components/base/captcha.vue");
const VueSignaturePad = httpVueLoader("./src/components/base/VueSignaturePad.vue");
const mis_novedades = httpVueLoader("./src/pages/novedad/mis_novedades.php");

const routes = [
    { path: '/', component: LayouthHome, props: true,

        children: [
            {
                path: '/',
                component: inicio
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
	{ path: '/', component: inicio, props: true,

      children: [
            {
                 name:'/inicio',
            path: '/inicio',
            }
        ]
    },
	{ path: '/', component: nuevo, props: true,

      children: [
            {
                 name:'/nuevo',
            path: '/nuevo',
            }
        ]
    },
	{ path: '/', component: LayouthHome, props: true,

      children: [
            {
                 name:'/acerca',
            path: '/acerca',
			component: acerca
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
	{ path: '/', component: LayouthHome, props: true,

      children: [
            {
                 name:'/ingresar',
            path: '/ingresar',\
			 component: ingresar
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