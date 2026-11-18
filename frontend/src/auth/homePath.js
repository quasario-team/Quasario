// Where a signed-in person goes by default: admins to the admin panel, users to home.
export function homePathFor(user) {
  return user?.role === "admin" ? "/admin" : "/";
}
