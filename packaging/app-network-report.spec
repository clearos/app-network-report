
Name: app-network-report
Epoch: 1
Version: 1.4.3
Release: 1%{dist}
Summary: Network Report
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
The Network Report provides network throughput information on all your network interfaces.

%package core
Summary: Network Report - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-network-core >= 1:1.4.3
Requires: app-reports-core >= 1:1.4.2
Requires: app-reports-database-core >= 1:1.4.2
Requires: app-tasks-core

%description core
The Network Report provides network throughput information on all your network interfaces.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/network_report
cp -r * %{buildroot}/usr/clearos/apps/network_report/

install -D -m 0644 packaging/app-network-report.cron %{buildroot}/etc/cron.d/app-network-report
install -D -m 0755 packaging/network2db %{buildroot}/usr/sbin/network2db

%post
logger -p local6.notice -t installer 'app-network-report - installing'

%post core
logger -p local6.notice -t installer 'app-network-report-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/network_report/deploy/install ] && /usr/clearos/apps/network_report/deploy/install
fi

[ -x /usr/clearos/apps/network_report/deploy/upgrade ] && /usr/clearos/apps/network_report/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-network-report - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-network-report-core - uninstalling'
    [ -x /usr/clearos/apps/network_report/deploy/uninstall ] && /usr/clearos/apps/network_report/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/network_report/controllers
/usr/clearos/apps/network_report/htdocs
/usr/clearos/apps/network_report/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/network_report/packaging
%exclude /usr/clearos/apps/network_report/tests
%dir /usr/clearos/apps/network_report
/usr/clearos/apps/network_report/deploy
/usr/clearos/apps/network_report/language
/usr/clearos/apps/network_report/libraries
/etc/cron.d/app-network-report
/usr/sbin/network2db
