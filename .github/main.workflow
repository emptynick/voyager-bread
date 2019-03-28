workflow "Build and publish assets" {
  on = "push"
  resolves = ["Commit and push assets"]
}

action "Build assets" {
  uses = "emptynick/actions-js-build/build@master"
  args = "run dev"
}

action "Commit and push assets" {
  uses = "emptynick/actions-js-build/commit@master"
  needs = ["Build assets"]
  secrets = ["GITHUB_TOKEN"]
}
