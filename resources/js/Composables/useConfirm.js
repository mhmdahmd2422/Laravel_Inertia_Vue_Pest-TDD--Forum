import {reactive, readonly} from "vue";


const globalState = reactive({
    show: false,
    title: '',
    message: '',
    resolver: null,
});

export function useConfirm() {
    const resetModal = () => {
        globalState.show = false;
        globalState.title = '';
        globalState.message = '';
        globalState.resolver = null
    }

    return {
        state: readonly(globalState),
        confirmation(message, title = 'Please Confirm'){
            globalState.show = true;
            globalState.title = title;
            globalState.message = message;

            return new Promise((resolverState) => {
                globalState.resolver = resolverState;
            })
        },
        confirm: () => {
            if(globalState.resolver){
                globalState.resolver(true);
            }

            resetModal();
        },
        cancel: () => {
            if(globalState.resolver){
                globalState.resolver(false);
            }

            resetModal();
        },
    }
}
