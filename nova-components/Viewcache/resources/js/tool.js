Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'viewcache',
            path: '/viewcache',
            component: require('./components/Tool'),
        },
    ])
})
