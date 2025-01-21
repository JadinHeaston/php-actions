# PHP Actions

This is a repository dedicated to PHP CLI scripts that can (often) be used as Git actions.

## Guidelines

- Scripts should strive to be entirely self-contained where possible.
	- Some may have external dependencies. Limit dependencies where possible and do NOT use Composer.

> [!NOTE]
> The [includes folder](./includes/) contains shared code, although this code is directly copied into the script itself.  
> Modifications to one version of a function should be reflected in all others. If the version needs to be distinct, the name needs to be changed.

## Git Hooks

[Git Hooks](https://git-scm.com/book/ms/v2/Customizing-Git-Git-Hooks) are client side scripts that can run based on certain Git events.

Copy the corresponding hook(s) into `.git/hooks/` for the repository. (Most of these scripts are pre-commit hooks)

> [!IMPORTANT]  
> It's important that the hook script itself is executable.
> You can use the following command: `chmod +x <HOOK_NAME>`

> [!NOTE]  
> If multiple pre-commit scripts are used, custom modification may be required.
