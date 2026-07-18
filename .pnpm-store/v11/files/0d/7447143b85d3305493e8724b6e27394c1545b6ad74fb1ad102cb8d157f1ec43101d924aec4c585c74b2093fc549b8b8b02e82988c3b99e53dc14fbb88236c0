import type { ThemeJson } from "../types.js";
/**
 * Deep merge source into target, mutating target.
 * Objects are recursively merged; arrays and primitives are overwritten.
 */
export declare function deepMerge(target: Record<string, unknown>, source: Record<string, unknown>): Record<string, unknown>;
/**
 * Resolve partial directory paths relative to the project root.
 */
export declare function resolvePartialDirs(partials: string | string[], rootDir: string): string[];
/**
 * Find all *.theme.js and *.theme.json files under a directory,
 * skipping node_modules, vendor, dist, and public directories.
 */
export declare function findPartialFiles(rootDir: string): string[];
/**
 * Load and merge partial theme files into the theme.json.
 */
export declare function mergePartials(themeJson: ThemeJson, files: string[]): Promise<void>;
