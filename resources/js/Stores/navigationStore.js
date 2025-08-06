import { defineStore } from 'pinia';

export const useNavigationStore = defineStore('navigation', {
    state: () => ({
        menuItems: [
            { name: "About Us", href: "/about" },
            { name: "Divisions", href: "/division" },
            { name: "Ministries", href: "/ministry" },
            { name: "Devotions", href: "/devotion" },
            { name: "Contact Us", href: "/contact" },
        ],
    }),
    getters: {
        getMenuItems: (state) => state.menuItems,
    },
});
