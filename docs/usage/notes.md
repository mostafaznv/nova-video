# Notes

### Pruning Files

If you are using `Larupload`, Nova's <mark style="color:red;">prunable</mark> method does not work with `NovaVideo` field as expected. As you may know, in `Larupload`, there is an option to turn on/off <mark style="color:red;">`preserve-files`</mark>. This option is used to prevent files from being deleted when the model is deleted from the database, and it aligns with the behavior expected from the `prunable` method. Therefore, if you want to keep files when the model is deleted, you should set `preserve-files` to `true`. You can do this either in your Larupload [configuration](https://mostafaznv.gitbook.io/larupload/advanced-usage/configuration/preserve-files) file or in your file [attachment instance](https://mostafaznv.gitbook.io/larupload/advanced-usage/attachment/preserve-files).



