import axios from '@nextcloud/axios'
import { generateOcsUrl } from '@nextcloud/router'

OCA.Files.fileActions.registerAction({
	name: 'myPdfAction',
	displayName: t('my-app-id', 'My PDF action'),
	mime: 'application/pdf',
	permissions: OC.PERMISSION_READ,
	iconClass: 'icon-file',
	actionHandler: async (name, context) => {
		// console.debug('anr resp ', resp)
		// OC.dialogs.info('The PDF file "' + name + '" has a size of ' + context.fileInfoModel.attributes.size, 'My PDF action')
		const id = context.fileInfoModel.attributes.id
		const url = generateOcsUrl('apps/fileswm/api/v1/fileswm/{id}', { id })
		console.debug('anr is here ', url)
		axios.get(url, { responseType: 'blob' }).then((res) => {
			console.debug('res ', res)
			const url = window.URL.createObjectURL(res.data)
			const link = document.createElement('a')
			link.href = url
			link.setAttribute('download', name)
			document.body.appendChild(link)
			link.click()
		})
	},
})
