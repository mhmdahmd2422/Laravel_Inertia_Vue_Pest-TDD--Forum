import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {useIntersect} from "@/Composables/useIntersect.js";

export function useInfiniteScroll(propName, loader){
    const value = () => usePage().props[propName];
    console.log(value());
    const items = ref(value().data);
    const initialUrl = usePage().url;
    const canLoadMoreItems = computed(() => value().links.next !== null);
    const loadMoreItems = () => {
        if(!canLoadMoreItems.value){
            return;
        }
        router.get(value().links.next, {}, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: () => {
                window.history.replaceState({}, '', initialUrl);
                items.value = [...items.value, ...value().data];
            },
        })
    };

    if(loader !== null){
        useIntersect(loader, loadMoreItems, {
            rootMargin: '0px 0px 170px 0px'
        });
    }

    return{
        items,
        loadMoreItems,
        reset: () => items.value = value().data,
        canLoadMoreItems,
    }
}
