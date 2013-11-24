package ladanse.website.domain;

import ladanse.website.domain.master.RoleType;
import ladanse.website.domain.master.SignUpType;

import javax.persistence.*;
import java.util.Date;
import java.util.HashSet;
import java.util.Set;

@Entity
@Table(name = "SignUp")
public class SignUp {
    private Long id;
    private Date since;
    private Date end;
    private Event event;
    private Account account;
    private SignUpType signUpType;
    private Set<ForRole> forRoles;

    protected SignUp() {
    }

    public SignUp(Account account, Event event, SignUpType signUpType, Set<RoleType> roleTypes) {
        setSignUpType(signUpType);
        setEvent(event);
        setAccount(account);
        setSince(new Date());

        Set<ForRole> roles = new HashSet<>();

        for (RoleType roleType : roleTypes) {
            ForRole forRole = new ForRole(this, roleType);

            roles.add(forRole);
        }

        setForRoles(roles);
    }

    public void cancel() {
        setEnd(new Date());
    }

    @Id
    @Column(name = "id")
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    public Long getId() {
        return id;
    }

    protected void setId(Long id) {
        this.id = id;
    }

    @Basic
    @Column(name = "since")
    @Temporal(TemporalType.TIMESTAMP)
    public Date getSince() {
        return since;
    }

    protected void setSince(Date since) {
        this.since = since;
    }

    @Basic
    @Column(name = "end")
    @Temporal(TemporalType.TIMESTAMP)
    public Date getEnd() {
        return end;
    }

    protected void setEnd(Date end) {
        this.end = end;
    }

    @ManyToOne
    @JoinColumn(name = "eventId", nullable = false)
    public Event getEvent() {
        return event;
    }

    protected void setEvent(Event event) {
        this.event = event;
    }

    @ManyToOne
    @JoinColumn(name = "accountId", nullable = false)
    public Account getAccount() {
        return account;
    }

    protected void setAccount(Account account) {
        this.account = account;
    }

    @Basic
    @Column(name = "signUpType")
    @Enumerated(EnumType.STRING)
    public SignUpType getSignUpType() {
        return signUpType;
    }

    protected void setSignUpType(SignUpType signUpType) {
        this.signUpType = signUpType;
    }

    @OneToMany(cascade = CascadeType.ALL, mappedBy = "signUp")
    public Set<ForRole> getForRoles() {
        return forRoles;
    }

    protected void setForRoles(Set<ForRole> forRoles) {
        this.forRoles = forRoles;
    }
}
