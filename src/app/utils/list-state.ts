import { computed, signal } from '@angular/core'
import { toObservable, toSignal } from '@angular/core/rxjs-interop'
import { catchError, map, of, startWith, switchMap } from 'rxjs'
import { Observable } from 'rxjs'

export interface ListResponse<T> {
    data:  T[]
    total: number
}

type ListState<T> =
    | { type: 'loading' }
    | { type: 'success'; data: T[]; total: number }
    | { type: 'error' }

const LOADING = { type: 'loading' } as const

export function createListState<T>(
    fetchFn: (params: Record<string, unknown>) => Observable<ListResponse<T>>,
    options: { pageSize?: number } = {}
) {
    const searchQuery    = signal('')
    const pageIndex      = signal(1)
    const pageSize       = signal(options.pageSize ?? 10)
    const refreshTrigger = signal(0)

    const params = computed(() => ({
        query:     searchQuery(),
        pageIndex: pageIndex(),
        pageSize:  pageSize(),
        refresh:   refreshTrigger(),
    }))

    const state = toSignal(
        toObservable(params).pipe(
            switchMap((p) =>
                fetchFn(p).pipe(
                    map((r) => ({ type: 'success', data: r.data, total: r.total } as ListState<T>)),
                    startWith(LOADING as ListState<T>),
                    catchError(() => of({ type: 'error' } as ListState<T>)),
                )
            ),
            startWith(LOADING as ListState<T>),
        ),
        { requireSync: true }
    )

    return {
        state,
        searchQuery,
        pageIndex,
        pageSize,
        refresh:    () => refreshTrigger.update((n) => n + 1),
        get items()     { const s = state(); return s.type === 'success' ? s.data  : [] },
        get total()     { const s = state(); return s.type === 'success' ? s.total : 0 },
        get isLoading() { return state().type === 'loading' },
        get hasError()  { return state().type === 'error' },
        get error()     { return state().type === 'error' },
    }
}
