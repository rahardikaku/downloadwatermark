import axios from '@nextcloud/axios'
import { generateOcsUrl } from '@nextcloud/router'
OCA.Files.fileActions.registerAction({
	name: 'DownloadSalinan',
	displayName: 'Download Salinan',
	mime: 'application/pdf',
	type: OCA.Files.FileActions.TYPE_INLINE,
	permissions: OC.PERMISSION_READ,
	iconClass: 'icon-file',
	actionHandler: async (name, context) => {
		const downloadFileaction = $(context.$file).find('.fileactions .action-download')
		// don't allow a second click on the download action
		if (downloadFileaction.hasClass('disabled')) {
			return
		}
		context.fileList.showFileBusyState(name, true)
		const id = context.fileInfoModel.attributes.id
		const url = generateOcsUrl('apps/fileswm/api/v1/fileswm/{id}', { id })
		axios.get(url, { responseType: 'blob' }).then((res) => {
			const url = window.URL.createObjectURL(res.data)
			const link = document.createElement('a')
			link.href = url
			link.setAttribute('download', name)
			document.body.appendChild(link)
			link.click()
			context.fileList.showFileBusyState(name, false)
		})
	},
})
