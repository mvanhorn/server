/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { cleanup, getAllByRole, render } from '@testing-library/vue'
import { beforeEach, describe, expect, it } from 'vitest'
import UpdaterAdmin from './UpdaterAdmin.vue'

const incompatibleApps = Object.freeze([
	{ id: 'deck', name: 'Deck' },
	{ id: 'groupfolders', name: 'Team folders' },
	{ id: 'richdocuments', name: 'Nextcloud Office' },
])

const appsToUpgrade = Object.freeze([
	{ id: 'files_sharing', name: 'File sharing', version: '1.2.0', oldVersion: '1.1.0' },
])

const defaultUpdateInfo = Object.freeze({
	appsToUpgrade: [] as typeof appsToUpgrade,
	incompatibleAppsList: [] as typeof incompatibleApps,
	isAppsOnlyUpgrade: false,
	oldTheme: null as string | null,
	productName: 'Nextcloud',
	version: '33.0.3',
})

describe('UpdaterAdmin incompatible app warning', () => {
	beforeEach(cleanup)
	beforeEach(() => {
		removeInitialState()
	})

	it('renders the complete warning and app list for a major upgrade', () => {
		seedUpdateInfo({
			appsToUpgrade,
			incompatibleAppsList: incompatibleApps,
		})
		const component = render(UpdaterAdmin)

		const warning = getIncompatibleAppsWarning(component)
		expect(warning.textContent).toContain('The currently installed versions of these apps are incompatible with Nextcloud 33.0.3 and will be disabled during the update:')
		expect(warning.textContent).toContain('Nextcloud will attempt to install compatible app updates and re-enable the apps. Apps without a successful compatible update will remain disabled.')

		const list = component.getByRole('list', { name: 'Incompatible apps' })
		const items = getAllByRole(list, 'listitem')
		expect(items).toHaveLength(incompatibleApps.length)
		incompatibleApps.forEach((app, index) => {
			expect(items[index]).toHaveTextContent(`${app.name} (${app.id})`)
			expect(countOccurrences(list.textContent ?? '', `${app.name} (${app.id})`)).toBe(1)
		})
	})

	it('hides the warning, explanation, and list when no apps are incompatible', () => {
		seedUpdateInfo({
			appsToUpgrade,
			incompatibleAppsList: [],
		})
		const component = render(UpdaterAdmin)

		expect(component.queryByRole('list', { name: 'Incompatible apps' })).toBeNull()
		expect(component.queryByText(/currently installed versions of these apps are incompatible/)).toBeNull()
		expect(component.queryByText(/will attempt to install compatible app updates/)).toBeNull()
		expect(component.queryByText(/will remain disabled/)).toBeNull()
	})

	it('still explains attempted app-store recovery when appsToUpgrade is empty', () => {
		seedUpdateInfo({
			appsToUpgrade: [],
			incompatibleAppsList: incompatibleApps,
		})
		const component = render(UpdaterAdmin)

		const warning = getIncompatibleAppsWarning(component)
		expect(warning.textContent).toContain('The currently installed versions of these apps are incompatible with Nextcloud 33.0.3 and will be disabled during the update:')
		expect(warning.textContent).toContain('Nextcloud will attempt to install compatible app updates and re-enable the apps. Apps without a successful compatible update will remain disabled.')
		expect(component.getByRole('list', { name: 'Incompatible apps' })).not.toBeNull()
	})

	it('keeps the explanation conditional during an apps-only upgrade', () => {
		seedUpdateInfo({
			appsToUpgrade,
			incompatibleAppsList: incompatibleApps.slice(0, 1),
			isAppsOnlyUpgrade: true,
		})
		const component = render(UpdaterAdmin)

		expect(component.getByRole('heading', { level: 2 })).toHaveTextContent('App update required')
		expect(component.queryByText(/will be updated to version/)).toBeNull()

		const warning = getIncompatibleAppsWarning(component)
		expect(warning.textContent).toContain('The currently installed versions of these apps are incompatible with Nextcloud 33.0.3 and will be disabled during the update:')
		expect(warning.textContent).toContain('Nextcloud will attempt to install compatible app updates and re-enable the apps. Apps without a successful compatible update will remain disabled.')
		expect(warning.textContent).not.toMatch(/major version/i)
		expect(warning.textContent).not.toMatch(/will be (updated|enabled|re-enabled)/i)
	})

	it('interpolates a custom product name and target version from initial state', () => {
		seedUpdateInfo({
			incompatibleAppsList: incompatibleApps,
			productName: 'Acme Cloud',
			version: '34.0.1',
		})
		const component = render(UpdaterAdmin)

		const warning = getIncompatibleAppsWarning(component)
		expect(warning.textContent).toContain('The currently installed versions of these apps are incompatible with Acme Cloud 34.0.1 and will be disabled during the update:')
		expect(warning.textContent).toContain('Acme Cloud will attempt to install compatible app updates and re-enable the apps. Apps without a successful compatible update will remain disabled.')
		expect(warning.textContent).not.toContain('Nextcloud 33.0.3')
		expect(warning.textContent).not.toContain('incompatible with Nextcloud')
	})
})

/**
 * Render the incompatibility warning note that contains the incompatible-apps list.
 *
 * @param component - The rendered updater view
 */
function getIncompatibleAppsWarning(component: ReturnType<typeof render>): HTMLElement {
	const list = component.getByRole('list', { name: 'Incompatible apps' })
	const warning = list.closest('[role="note"]')
	expect(warning).not.toBeNull()
	return warning as HTMLElement
}

/**
 * Count how many times a snippet occurs in a string.
 *
 * @param text - The haystack
 * @param snippet - The needle
 */
function countOccurrences(text: string, snippet: string): number {
	return text.split(snippet).length - 1
}

/**
 * Seed the updater initial state, merging with defaults.
 *
 * @param overrides - Partial updateInfo to merge
 */
function seedUpdateInfo(overrides: Partial<typeof defaultUpdateInfo> = {}): void {
	mockInitialState('core', 'updateInfo', {
		...defaultUpdateInfo,
		...overrides,
	})
}

/**
 * Remove the mocked initial state
 */
function removeInitialState(): void {
	document.querySelectorAll('input[type="hidden"]').forEach((el) => {
		el.remove()
	})
	// clear the cache
	delete globalThis._nc_initial_state
}

/**
 * Helper to mock an initial state value
 * @param app - The app
 * @param key - The key
 * @param value - The value
 */
function mockInitialState(app: string, key: string, value: unknown): void {
	const el = document.createElement('input')
	el.value = btoa(JSON.stringify(value))
	el.id = `initial-state-${app}-${key}`
	el.type = 'hidden'

	document.head.appendChild(el)
}
