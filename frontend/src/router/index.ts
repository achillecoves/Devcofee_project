import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  scrollBehavior(to, from, savedPosition) {
    // always scroll to top
    return { top: 0 }
  },
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../features/ConsumptionRecord/views/ConsumptionRecord.vue'),
    },
    {
      path: '/history',
      name: 'history',
      component: () => import('../features/ConsumptionHistory/views/ConsumptionHistory.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'Erreur 404',
      component: () => import('../features/ConsumptionRecord/views/errors/404.vue'),
    },
  ],
})

export default router