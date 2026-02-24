import { useState, useEffect, useCallback } from 'react';

export default function StatesPage({ token }) {
    const [states, setStates] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selected, setSelected] = useState(null);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState(null);

    const fetchStates = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const res = await fetch(`/api/states?page=${page}&pageSize=10`, {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Geonames ${token}`,
                },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            setStates(data.data);
            setMeta({ current: data.current_page, last: data.last_page, total: data.total });
        } catch (e) {
            setError(e.message);
        } finally {
            setLoading(false);
        }
    }, [token, page]);

    useEffect(() => { fetchStates(); }, [fetchStates]);

    const fetchOne = async (id) => {
        try {
            const res = await fetch(`/api/states/${id}`, {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Geonames ${token}`,
                },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            setSelected(await res.json());
        } catch (e) {
            setError(e.message);
        }
    };

    return (
        <div>
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h2 className="text-2xl font-bold">States</h2>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <code className="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-xs">GET /api/states</code>
                        {meta && <span className="ml-2">{meta.total} total</span>}
                    </p>
                </div>
                <button
                    onClick={() => { setSelected(null); fetchStates(); }}
                    className="text-sm px-3 py-1.5 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors"
                >
                    Refresh
                </button>
            </div>

            {error && (
                <div className="mb-4 p-3 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-400 text-sm">
                    {error}
                </div>
            )}

            {selected ? (
                <div>
                    <button
                        onClick={() => setSelected(null)}
                        className="text-sm text-blue-600 dark:text-blue-400 hover:underline mb-4 inline-flex items-center gap-1"
                    >
                        ← Back to list
                    </button>
                    <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6">
                        <h3 className="text-lg font-semibold mb-1">
                            <code className="text-xs text-gray-500 dark:text-gray-400 font-normal">GET /api/states/{selected.id}</code>
                        </h3>
                        <pre className="mt-4 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-sm overflow-x-auto font-mono">
                            {JSON.stringify(selected, null, 2)}
                        </pre>
                    </div>
                </div>
            ) : loading ? (
                <div className="text-center py-12 text-gray-400">Loading...</div>
            ) : states.length === 0 ? (
                <div className="text-center py-12 text-gray-400">No states found. Create some via the API.</div>
            ) : (
                <>
                    <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950">
                                    <th className="text-left px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400">ID</th>
                                    <th className="text-left px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400">Name</th>
                                    <th className="text-left px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400">Short Name</th>
                                    <th className="text-left px-4 py-2.5 font-medium text-gray-500 dark:text-gray-400">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                {states.map((s) => (
                                    <tr
                                        key={s.id}
                                        onClick={() => fetchOne(s.id)}
                                        className="border-b last:border-b-0 border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs text-gray-500">{s.id}</td>
                                        <td className="px-4 py-3 font-medium">{s.name}</td>
                                        <td className="px-4 py-3">
                                            <span className="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded text-xs font-semibold">
                                                {s.shortName}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-gray-500 text-xs">{s.createdAt}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {meta && meta.last > 1 && (
                        <div className="flex items-center justify-center gap-2 mt-4">
                            <button
                                disabled={page <= 1}
                                onClick={() => setPage((p) => p - 1)}
                                className="px-3 py-1.5 text-sm rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 disabled:opacity-40"
                            >
                                Previous
                            </button>
                            <span className="text-sm text-gray-500">{meta.current} / {meta.last}</span>
                            <button
                                disabled={page >= meta.last}
                                onClick={() => setPage((p) => p + 1)}
                                className="px-3 py-1.5 text-sm rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 disabled:opacity-40"
                            >
                                Next
                            </button>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
