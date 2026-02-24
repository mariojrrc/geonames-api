import { useState } from 'react';
import StatesPage from './StatesPage';
import CitiesPage from './CitiesPage';

const DEFAULT_TOKEN = 'b17d8756cc299c0c897454ee4dd0e58';

export default function App() {
    const [page, setPage] = useState('states');
    const [token, setToken] = useState(DEFAULT_TOKEN);

    const nav = [
        { id: 'states', label: 'States' },
        { id: 'cities', label: 'Cities' },
    ];

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
            <header className="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
                    <div className="flex items-center gap-6">
                        <h1 className="text-lg font-semibold tracking-tight whitespace-nowrap">
                            GeoNames API
                        </h1>
                        <nav className="flex gap-1">
                            {nav.map((n) => (
                                <button
                                    key={n.id}
                                    onClick={() => setPage(n.id)}
                                    className={`px-3 py-1.5 text-sm rounded-md font-medium transition-colors ${
                                        page === n.id
                                            ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900'
                                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-800'
                                    }`}
                                >
                                    {n.label}
                                </button>
                            ))}
                        </nav>
                    </div>
                    <div className="flex items-center gap-2">
                        <label className="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Token:</label>
                        <input
                            type="text"
                            value={token}
                            onChange={(e) => setToken(e.target.value)}
                            className="text-xs font-mono bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded px-2 py-1 w-64"
                        />
                    </div>
                </div>
            </header>

            <main className="max-w-5xl mx-auto px-4 sm:px-6 py-8">
                {page === 'states' && <StatesPage token={token} />}
                {page === 'cities' && <CitiesPage token={token} />}
            </main>
        </div>
    );
}
