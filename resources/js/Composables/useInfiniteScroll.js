import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {useIntersect} from "@/Composables/useIntersect.js";

export function useInfiniteScroll(propName, loader){
    const value = () => usePage().props[propName];
    const items = ref(value().data);
    const initialUrl = usePage().url;
    const canLoadMoreItems = computed(() => {
        if(propName === 'posts'){
            return value().next_page_url !== null
        } else {
            return value().links.next !== null
        }
    });
    const loadMoreItems = () => {
        if(!canLoadMoreItems.value){
            return;
        }
        if(propName === 'posts'){
            router.get(value().next_page_url, {}, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => {
                    window.history.replaceState({}, '', initialUrl);
                    items.value = [...items.value, ...value().data];
                },
            });
        } else {
            router.get(value().links.next, {}, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => {
                    window.history.replaceState({}, '', initialUrl);
                    items.value = [...items.value, ...value().data];
                },
            });
        }
    };

    if(loader !== null){
        useIntersect(loader, loadMoreItems, {
            rootMargin: '0px 0px 200px 0px'
        });
    }

    return{
        items,
        loadMoreItems,
        reset: () => items.value = value().data,
        canLoadMoreItems,
    }
}
